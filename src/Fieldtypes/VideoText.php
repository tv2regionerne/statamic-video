<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Captioning\Format\WebvttCue;
use Captioning\Format\WebvttFile;
use Statamic\Fields\Fieldtype;

class VideoText extends Fieldtype
{
    protected function configFieldItems(): array
    {
        return [
            'source' => [
                'type' => 'text',
                'display' => __('Video Source Field'),
                'instructions' => __('What video field handle the Timemer should use.'),
                'default' => 'video',
                'width' => 50,
                'validate' => [
                    'required',
                ],
            ],
        ];
    }

    public function preProcess($data)
    {
        if (! isset($data)) {
            return [
                [
                    'start' => 0,
                    'end' => 0,
                    'text' => null,
                ],
            ];
        }

        $vtt = new WebVttFile();
        $vtt->loadFromString(trim($data));

        return collect($vtt->getCues())
            ->map(function (WebvttCue $cue) {
                return [
                    'start' => (int) ($cue->getStartMS()),
                    'end' => (int) ($cue->getStopMS()),
                    'text' => $cue->getText(),
                ];
            })
            ->all();
    }

    public function process($data)
    {
        if (! is_array($data)) {
            return null;
        }

        $vtt = new WebvttFile();

        collect($data)
            ->each(function ($item, $i) use ($vtt) {
                $vtt->addCue((new WebvttCue('00:00:00:00', '00:00:00:00'))
                    ->setStartMS($item['start'])
                    ->setStopMS($item['end'])
                    ->setText($item['text'] ?? 'Unnamed'));
            });

        $vtt->build();

        return $vtt->getFileContent();
    }

    public function augment($value): array
    {
        $output = [
            'raw' => $value,
            'error' => false,
        ];

        try {
            $vtt = new WebvttFile();
            $vtt->loadFromString(trim($value));

            $cues = [];

            /** @var WebvttCue $cue */
            foreach ($vtt->getCues() as $cue) {
                $cues[] = [
                    'start' => $cue->getStart(),
                    'stop' => $cue->getStop(),
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
