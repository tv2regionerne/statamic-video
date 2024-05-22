<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Captioning\Format\WebvttCue;
use Captioning\Format\WebVttFile;
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

    public function defaultValue()
    {
        return [
            [
                'start' => 0,
                'text' => null,
            ],
        ];
    }

    public function preProcess($data)
    {
        $vtt = new WebVttFile();
        $vtt->loadFromString(trim($data));

        return collect($vtt->getCues())
             ->map(function (WebvttCue $cue) {
                 return [
                     'start' => (int) ($cue->getStartMS() / 1000),
                     'text' => $cue->getText(),
                 ];
             })
             ->all();
    }

    public function process($data)
    {
        $vtt = new WebvttFile();

        collect($data)
            ->each(function ($item, $i) use ($vtt, $data) {
                $start = $item['start'];
                $end = $data[$i + 1]['start'] ?? 3600; // @todo we need the video duration, or we need to pass it in
                $vtt->addCue((new WebvttCue('00:00:00:00', '00:00:00:00'))
                    ->setStartMS($start * 1000)
                    ->setStopMS($end * 1000)
                    ->setText($item['text'] ?? 'Unnamed'));
            });

        $vtt->build();

        return $vtt->getFileContent();
    }
}
