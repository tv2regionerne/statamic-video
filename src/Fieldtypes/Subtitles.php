<?php

namespace Cboxdk\StatamicSubtitles\Fieldtypes;

use Captioning\Format\WebvttCue;
use Captioning\Format\WebvttFile;
use Captioning\Format\WebvttRegion;
use Statamic\Fields\Fieldtype;

class Subtitles extends Fieldtype
{
    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        $vtt = new WebvttFile();
        $vtt->loadFromString($data);
        return $data;
    }

    public function augment($value): array
    {
        $output = [
            'error' => false
        ];
        try {
            $vtt = new WebvttFile();
            $vtt->loadFromString($value);

            $cues = [];

            /** @var WebvttCue $cue */
            foreach ($vtt->getCues() as $cue) {

                $cues[] = [
                    'start' => $cue->getStart(),
                    'stop' => $cue->getStart(),
                    'text' => $cue->getText(),
                    //'textLines' => $cue->getTextLines(),
                ];
            }
            $output['cues'] = $cues;


        } catch (\Exception $exception) {
            $output['error'] = true;
            $output['error_msg'] = $exception->getMessage();
        }


        return $output;
    }
}
