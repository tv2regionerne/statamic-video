<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Captioning\Format\WebvttCue;
use Captioning\Format\WebvttFile;
use Illuminate\Http\UploadedFile;
use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\AssetContainer;
use Statamic\Fields\Fieldtype;
use Statamic\Support\Arr;
use Statamic\Support\Str;

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
            'mode' => [
                'display' => __('Mode'),
                'instructions' => __('Advanced mode allows you to add descripions and thumbnails.'),
                'type' => 'select',
                'default' => 'basic',
                'options' => [
                    'basic' => __('Basic'),
                    'advanced' => __('Advanced'),
                ],
            ],
            'container' => [
                'display' => __('Container'),
                'instructions' => __('Choose which asset container to store the thumbnails in.'),
                'type' => 'asset_container',
                'max_items' => 1,
                'mode' => 'select',
                'validate' => 'required_if:mode,advanced',
                'default' => AssetContainer::all()->count() == 1 ? AssetContainer::all()->first()->handle() : null,
                'if' => [
                    'mode' => 'advanced',
                ],
            ],
            'folder' => [
                'display' => __('Folder'),
                'instructions' => __('Choose which folder to store the thumbnails in.'),
                'type' => 'asset_folder',
                'max_items' => 1,
                'if' => [
                    'mode' => 'advanced',
                ],
            ],
        ];
    }

    public function preload()
    {
        return [
            'id' => $this->parentId(),
        ];
    }

    public function preProcess($data)
    {
        if (! isset($data)) {
            $data = [[
                'start' => 0,
                'end' => 0,
                'text' => null,
                'description' => null,
                'thumbnail' => null,
            ]];
        }

        if (is_string($data)) {
            $data = $this->vttToData($data);
        }

        foreach ($data as $i => $item) {
            if ($item['thumbnail'] ?? null) {
                $data[$i]['thumbnail'] = $this->preProcessThumbnail($item['thumbnail']);
            }
        }

        return $data;
    }

    public function process($data)
    {
        if (is_string($data)) {
            $data = $this->vttToData($data);
        }

        foreach ($data as $i => $item) {
            if ($item['thumbnail'] ?? null) {
                $data[$i]['thumbnail'] = $this->processThumbnail($item['thumbnail'], $i + 1);
            }
        }

        return $data;
    }

    public function preProcessThumbnail($value)
    {
        $container = AssetContainer::find($this->config('container'));
        if (! $container) {
            return null;
        }

        return $container->asset($value)?->url();
    }

    public function processThumbnail($value, $number)
    {
        $container = AssetContainer::find($this->config('container'));
        if (! $container) {
            return null;
        }

        if (! Str::startsWith($value, 'data:')) {
            return ltrim(Str::after($value, $container->url()), '/');
        }

        $folder = $this->config('folder');
        $handle = $this->field->handle();
        $parentId = $this->parentId();

        $name = $handle.'-'.$number.'.jpg';
        $path = ltrim($folder.'/'.$parentId.'/'.$name);

        $data = base64_decode(Arr::last(explode(',', $value)));

        $tempFile = tmpfile();
        $tempPath = stream_get_meta_data($tempFile)['uri'];
        file_put_contents($tempPath, $data);
        app()->terminating(function () use ($tempFile) {
            fclose($tempFile);
        });

        $upload = new UploadedFile($tempPath, $name, null, 0, true);
        $asset = $container->makeAsset($path)->upload($upload);

        return $asset->path();
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
        $vtt = new WebvttFile;

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
        $vtt = new WebVttFile;
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

    protected function parentId()
    {
        $parent = $this->field->parent();

        return $parent instanceof Entry
            ? $parent->id()
            : null;
    }
}
