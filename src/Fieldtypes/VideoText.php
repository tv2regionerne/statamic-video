<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Captioning\Format\WebvttCue;
use Captioning\Format\WebvttFile;
use Facades\Statamic\Fieldtypes\RowId;
use Illuminate\Http\UploadedFile;
use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\AssetContainer;
use Statamic\Fields\Field;
use Statamic\Fields\Fieldtype;
use Statamic\Fieldtypes\Assets\Assets;
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
        $data = $this->field->value() ?? [];

        $thumbnails = collect($data)
            ->mapWithKeys(fn ($item) => [$item['id'] => $this->preloadThumbnail($item)])
            ->all();

        return [
            'id' => $this->parentId(),
            'thumbnails' => $thumbnails,
        ];
    }

    protected function preloadThumbnail($item)
    {
        if (! $this->config('container')) {
            return null;
        }
        $container = AssetContainer::find($this->config('container'));
        if (! $container) {
            return null;
        }

        $thumbnail = $item['thumbnail'] ?? null;
        if (! $thumbnail) {
            return null;
        }

        return $container->asset($thumbnail)?->thumbnailUrl();
    }

    public function preProcess($data)
    {
        if (is_string($data)) {
            $data = $this->vttToData($data);
        }

        $data = collect($data)
            ->map(fn ($item) => [
                ...$item,
                'id' => $item['id'] ?? RowId::generate(),
            ])
            ->all();

        return $data;
    }

    public function process($data)
    {
        if (is_string($data)) {
            $data = $this->vttToData($data);
        }

        $data = collect($data)
            ->map(fn ($item) => [
                ...$item,
                'id' => $item['id'] ?? RowId::generate(),
                'description' => $item['description'] ?? null,
            ])
            ->map(fn ($item) => [
                ...$item,
                'thumbnail' => $this->processThumbnail($item),
            ])
            ->all();

        return $data;
    }

    protected function processThumbnail($item)
    {
        if (! $this->config('container')) {
            return null;
        }
        $container = AssetContainer::find($this->config('container'));
        if (! $container) {
            return null;
        }

        $id = $item['id'];
        $thumbnail = $item['thumbnail'] ?? null;
        if (! Str::startsWith($thumbnail, 'data:')) {
            return $thumbnail;
        }

        $folder = $this->config('folder');
        $handle = $this->field->handle();
        $parentId = $this->parentId();

        $name = $handle.'-'.$id.'.jpg';
        $path = ltrim($folder.'/'.$parentId.'/'.$name);

        $data = base64_decode(Arr::last(explode(',', $thumbnail)));

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

    public function augment($value)
    {
        if (is_string($value)) {
            $value = $this->vttToData($value);
        }

        $value = collect($value)
            ->map(fn ($item) => [
                ...$item,
                'thumbnail' => $this->augmentThumbnail($item),
            ])
            ->all();

        return [
            'raw' => $this->dataToVtt($value),
            'cues' => $value,
        ];
    }

    public function shallowAugment($values)
    {
        $value = $this->augment($values);

        $value['cues'] = collect($value['cues'])
            ->map(fn ($item) => [
                ...$item,
                'thumbnail' => $item['thumbnail']?->toShallowAugmentedCollection(),
            ])->all();

        return $value;
    }

    protected function augmentThumbnail($item)
    {
        if (! $this->config('container')) {
            return null;
        }
        $container = AssetContainer::find($this->config('container'));
        if (! $container) {
            return null;
        }

        $thumbnail = $item['thumbnail'] ?? null;
        if (! $thumbnail) {
            return null;
        }

        $field = (new Assets)->setField(new Field('thumbnail', [
            'type' => 'assets',
            'max_files' => 1,
            'container' => $container->handle(),
        ]));

        return $field->augment($thumbnail);
    }

    protected function dataToVtt($data)
    {
        if (! count($data)) {
            return null;
        }

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
        try {
            $vtt = new WebVttFile;
            $vtt->loadFromString(trim($value));

            $data = collect($vtt->getCues())
                ->map(function (WebvttCue $cue) {
                    return [
                        'id' => RowId::generate(),
                        'start' => (int) ($cue->getStartMS()),
                        'end' => (int) ($cue->getStopMS()),
                        'text' => $cue->getText(),
                        'description' => null,
                        'thumbnail' => null,
                    ];
                })
                ->all();

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function parentId()
    {
        $parent = $this->field->parent();

        return $parent instanceof Entry
            ? $parent->id()
            : null;
    }
}
