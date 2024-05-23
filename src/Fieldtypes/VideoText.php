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
                    'start' => (int) ($cue->getStartMS() / 1000),
                    'end' => (int) ($cue->getStopMS() / 1000),
                    'text' => $cue->getText(),
                ];
            })
            ->all();
    }

    public function process($data)
    {
        $vtt = new WebvttFile();

        collect($data)
            ->each(function ($item, $i) use ($vtt) {
                $vtt->addCue((new WebvttCue('00:00:00:00', '00:00:00:00'))
                    ->setStartMS($item['start'] * 1000)
                    ->setStopMS($item['end'] * 1000)
                    ->setText($item['text'] ?? 'Unnamed'));
            });

        $vtt->build();

        return $vtt->getFileContent();
    }
}
