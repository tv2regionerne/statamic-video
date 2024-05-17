<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Statamic\Fields\Fieldtype;

class VideoTrim extends Fieldtype
{
    protected static $title = 'Video Trim';

    protected function configFieldItems(): array
    {
        return [
            'source' => [
                'type' => 'text',
                'display' => __('Video Source Field'),
                'instructions' => __('What video field handle the trimmer should use.'),
                'default' => 'video',
                'width' => 50,
                'validate' => [
                    'required',
                ],
            ],
        ];
    }
}
