<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Posts extends Component
{
    use WithPagination, WithFileUploads;

    public $title;
    public $slug_url;
    public $body;
    public $image;
    public $image_name;

    public $modalFormVisible = false;



    public function showCreateModal()
    {
        $this->modalFormVisible = true;
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


    public function rules()
    {
        return [
            'title'     => ['required'],
            'slug_url'  => ['required', Rule::unique('posts', 'slug')],
            'body'      => ['required'],
            'image'     => ['required', 'max:1024']
        ];
    }

    public function modelFormReset()
    {
        $this->title        = null;
        $this->slug_url     = null;
        $this->body         = null;
        $this->image        = null;
        $this->image_name   = null;
    }


    public function updatedTitle($val)
    {
        $this->slug_url = Str::slug($val);
    }


    public function store() 
    {
        $this->validate();  //it uses rules function

        if($this->image != '') {
            $this->image_name = md5($this->image . microtime()) . '.' . $this->image->extension();
            $this->image->storeAs('/', $this->image_name, 'uploads');
        }

        auth()->user()->posts()->create($this->modelData());

        $this->modelFormReset();

        $this->modalFormVisible = false;

        $this->alert('success', 'Post added successfully', [
            'position' =>  'center', 
            'timer' =>  3000,  
            'toast' =>  true, 
            'text' =>  '', 
            'confirmButtonText' =>  'Ok', 
            'cancelButtonText' =>  'Cancel', 
            'showCancelButton' =>  false, 
            'showConfirmButton' =>  false, 
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
