<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Statamic\Fields\Fieldtype;

class VideoTime extends Fieldtype
{
    protected static $title = 'Video Time';

    protected function configFieldItems(): array
    {
        return [
            'source' => [
                'type' => 'text',
                'display' => __('Video Source Field'),
                'default' => 'video',
                'width' => 50,
                'validate' => [
                    'required',
                ],
            ],
            'mode' => [
                'display' => __('Mode'),
                'type' => 'select',
                'default' => 'single',
                'options' => [
                    'single' => __('Single'),
                    'range' => __('Range'),
                ],
            ],
        ];
    }

    public function preProcess($value)
    {
        if (! isset($value)) {
            return $value;
        }

        return $this->config('mode') === 'range'
            ? $value
            : ['start' => $value, 'end' => null];
    }

    public function process($value)
    {
        if (! isset($value)) {
            return $value;
        }

        return $this->config('mode') === 'range'
            ? $value
            : $value['start'];
    }
}
