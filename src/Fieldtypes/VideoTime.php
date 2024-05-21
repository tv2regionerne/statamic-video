<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Statamic\Fields\Fieldtype;

class VideoTime extends Fieldtype
{
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
            'null_limits' => [
                'display' => __('Null Limits'),
                'type' => 'toggle',
            ],
        ];
    }

    public function defaultValue()
    {
        return $this->config('mode') === 'range'
            ? ['start' => null, 'end' => null]
            : null;
    }

    public function preProcess($value)
    {
        return $this->config('mode') === 'range'
            ? $value
            : ['start' => $value, 'end' => null];
    }

    public function process($value)
    {
        return $this->config('mode') === 'range'
            ? $value
            : $value['start'];
    }
}
