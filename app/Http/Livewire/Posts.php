<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use App\Helper\MySlugHelper;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;


class Posts extends Component
{
    use WithPagination, WithFileUploads;

    public $title;
    public $slug_url;
    public $body;
    public $modalId;
    public $image;
    public $image_name;

    public $modalFormVisible = false;
    public $confirmPostDelete = false;



    public function showCreateModal()
    {
        $this->emit('createNewPostEmit');
        $this->modelFormReset();
        $this->modalFormVisible = true;
    }

    public function showUpdateModal($id)
    {
        $this->emit('updatePostEmit');
        $this->modelFormReset();
        $this->modalId = $id;
        $this->modalFormVisible = true;
        $this->getModalData();
    }

    public function showDeleteModal($id)
    {
        $this->confirmPostDelete = true;
        $this->modalId = $id;
    }


    public function modelData()
    {
        return [
            'title' => $this->title,
            'slug'  => $this->slug_url,
            'body'  => $this->body,
            'image' => $this->image_name,
        ];
    }

    public function getModalData() 
    {
        $data = Post::find($this->modalId);
        $this->title        = $data->title;
        $this->slug_url     = $data->slug;
        $this->body         = $data->body;
        $this->image_name   = $data->image;
    }


    public function rules()
    {
        return [
            'title'     => ['required'],
            'slug_url'  => ['required', Rule::unique('posts', 'slug')->ignore($this->modalId)],
            'body'      => ['required'],
            'image'     => [Rule::requiredIf(!$this->modalId), 'max:1024']
        ];
    }

    public function modelFormReset()
    {
        $this->title        = null;
        $this->slug_url     = null;
        $this->body         = null;
        $this->image        = null;
        $this->image_name   = null;
        $this->modalId      = null;
    }


    public function updatedTitle($val)  //change slug when change title automatically
    {
        $this->slug_url = MySlugHelper::slug($val);
    }


    public function store() 
    {
        $this->validate();  //it uses rules function

        if($this->image != '') {
            $this->image_name = md5($this->image . microtime()) . '.' . $this->image->getClientOriginalExtension();
            $this->image->storeAs('/', $this->image_name, 'uploads');
        }

        auth()->user()->posts()->create($this->modelData());

        $this->modelFormReset();

        $this->modalFormVisible = false;

        $this->alert('success', 'Post added successful!', [
            'position'  =>  'center',
            'timer'  =>  3000,
            'toast'  =>  true,
            'text'  =>  null,
            'showCancelButton'  =>  false,
            'showConfirmButton'  =>  false
        ]);

    }


    public function update()
    {
        $this->validate();
        
        $post = Post::whereId($this->modalId)->first();

        if($this->image != '') {
            if($post->image != '') {
                if(File::exists("images/" . $post->image)) {
                    unlink("images/" . $post->image);
                }
            } 
            $this->image_name = md5($this->image . microtime()) . '.' . $this->image->extension();
            $this->image->storeAs('/', $this->image_name, 'uploads');
        }
        $post->update($this->modelData());

        $this->modalFormVisible = false;
        $this->modelFormReset();

        $this->alert('success', 'Post updated successful!', [
            'position'  =>  'center',
            'timer'  =>  3000,
            'toast'  =>  true,
            'text'  =>  null,
            'showCancelButton'  =>  false,
            'showConfirmButton'  =>  false
        ]);
    }


    public function delete() 
    {
        $post = Post::whereId($this->modalId)->first();
        if($post->image != '') {
            if(File::exists('images/' . $post->image)) {
                unlink('images/' . $post->image);
            }
        }
        $post->delete();

        $this->confirmPostDelete = false;
        $this->resetPage();    //from livewire to return to page 1 after deleted

        $this->alert('success', 'Post deleted successful!', [
            'position'  =>  'center',
            'timer'  =>  3000,
            'toast'  =>  true,
            'text'  =>  null,
            'showCancelButton'  =>  false,
            'showConfirmButton'  =>  false
        ]);
    }


    public function all_posts()
    {
        return Post::orderByDesc('id')->paginate(5);
    }


    public function render()
    {
        return view('livewire.posts', [
            'posts' => $this->all_posts()
        ]);
    }
}
