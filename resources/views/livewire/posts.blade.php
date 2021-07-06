<div>
    
    <div class="flex items-center justify-end py-4 text-right">
        <x-jet-button wire:click="showCreateModal">
            {{ __('Create Post') }}
        </x-jet-button>
    </div>

    <table class="w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-blue-500 tracking-wider">ID</th>
                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-blue-500 tracking-wider">Image</th>
                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-blue-500 tracking-wider">Title</th>
                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-blue-500 tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($posts as $post)
            <tr>
                <td class="px-6 py-3 border-b border-gray-200">{{ $post->id }}</td>
                <td class="px-6 py-3 border-b border-gray-200"><img src="{{ asset('images/' . $post->image) }}" alt="{{ $post->title }}" width="80"></td>
                <td class="px-6 py-3 border-b border-gray-200">{{ $post->title }}</td>
                <td class="px-6 py-3 border-b border-gray-200">
                <div class="flex items-center justify-end py-4 text-right">
                    <x-jet-button>
                        {{ __('Edit') }}
                    </x-jet-button>

                    <x-jet-danger-button class="ml-1">
                        {{ __('Delete') }}
                    </x-jet-button>
                </div>
                
                </td>
            </tr>
            @empty
            <tr>
                <td class="px-6 py-3 border-b border-gray-200" colspan="4">No posts found</td>
            </tr>   
            @endforelse
        </tbody>
    </table>

    <div class="pt-4">
        {!! $posts->links() !!}
    </div>
    

    <x-jet-dialog-modal wire:model="modalFormVisible">
        <x-slot name="title">
            {{ __('Create Post') }}
        </x-slot>

        <x-slot name="content">

            <div class="mt-4">
                <x-jet-label for="title" value="{{ __('Title') }}"></x-jet-label>
                <x-jet-input type="text" id="title" wire:model.debounce.500ms="title" class="block mt-1 w-full"></x-jet-input>
                @error('title')<span class="text-red-900 text-xl">{{ $message }}</span>@enderror
            </div>


            <div class="mt-4">
                <x-jet-label for="slug" value="{{ __('Slug') }}"></x-jet-label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3  border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        {{ config('app.url') . '/' }}
                    </span>

                    <input type="text" wire:model="slug_url" 
                    class="block w-full form-input flex-1 rounded-none rounded-r-md transition duration-150 ease-in-out sm:text-sm sm:leading-5" 
                    placeholder="url slug"></input>
                </div>
                @error('slug')<span class="text-red-900 text-xl">{{ $message }}</span>@enderror
            </div>

            <div class="mt-4">
                <x-jet-label for="body" value="{{ __('Content') }}"></x-jet-label>

                <div wire:ignore wire:key="myId">
                    <div id="body" class="block mt-1 w-full">
                        {!! $body !!}
                    </div>    
                </div>

                <textarea id="body" class="hidden body-content" wire:model.debounce.2000ms="body">
                    {!! $body !!}
                </textarea>
                @error('body')<span class="text-red-900 text-xl">{{ $message }}</span>@enderror
            </div>


            <div class="mt-4">
                <x-jet-label for="image" value="{{ __('Image') }}"></x-jet-label>

                <div class="flex py-3">
                    @if($image)
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center p-3 rounded border border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                <img src="{{ $image->temporaryUrl() }}" width="200">
                            </span>
                        </div>
                    @endif
                </div>

                <input type="file" id="image" wire:model="image" class="form-input block w-full form-input flex-1 rounded-none rounded-r-md transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                @error('image')<span class="text-red-900 text-xl">{{ $message }}</span>@enderror
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button>{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-button wire:click="store">{{ __('Create Post') }}</x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

</div>

@push('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js"></script>
<script>

    window.onload = function() {
        if(document.querySelector('#body')) {
            ClassicEditor.create(document.querySelector('#body'), {})
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    document.querySelector('#body').value = editor.getData();
                    @this.set('body', document.querySelector('#body').value);
                }); 
            })
            .catch(error => {
                console.log(error.stack);
            })
        }
    }

</script>
@endpush
