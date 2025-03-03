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
            $data = [[
                'start' => 0,
                'end' => 0,
                'text' => null,
            ]];
        }

        if (is_string($data)) {
            $data = $this->vttToData($data);
        }

        return $data;
    }

    public function process($data)
    {
        if (is_string($data)) {
            $data = $this->vttToData($data);
        }
        
        return $data;
    }

    public function augment($value): array
    {
        if (is_string($value)) {
            $value = $this->vttToData($value);
        }

        return [
            'raw' => $this->cuesToData($value),
            'cues' => $value,
            'error' => false,
        ];
    }

    protected function cuesToData($data)
    {
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

    protected function vttToData($value)
    {
        $vtt = new WebVttFile();
        $vtt->loadFromString(trim($value));

        $data = collect($vtt->getCues())
            ->map(function (WebvttCue $cue) {
                return [
                    'start' => (int) ($cue->getStartMS()),
                    'end' => (int) ($cue->getStopMS()),
                    'text' => $cue->getText(),
                ];
            })
            ->all();

        return $data;
    }
}
