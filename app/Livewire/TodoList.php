<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    public $name;
    public $search;

    public $EditingTodoId;
    public $EditingTodoName;

    public function create(){
        // validate
        $validated = $this->validate([
            'name' => 'required|min:3|max:50'
        ]);
        // create the todo
        Todo::create($validated);
        // clear the input
        $this->reset('name');
        // send falsh message
        session()->flash('success','Created');

        $this->resetPage();
    }

    public function toggle($todoId){
        $todo = Todo::findOrFail($todoId);

        $todo->completed = !$todo->completed;

        $todo->save();
    }

    public function delete(Todo $todo){
        $todo->delete();
    }

    public function edit($todoId){
        $this->EditingTodoId = $todoId;
        $this->EditingTodoName = Todo::findOrFail($todoId)->name;
    }

    public function cancelEdit(){
        $this->reset('EditingTodoId','EditingTodoName');
    }

    public function update(){
        $this->validate([
            'EditingTodoName' => 'required|min:3|max:50'
        ]);
        Todo::findOrFail($this->EditingTodoId)->update([
            'name' => $this->EditingTodoName
        ]);

        $this->cancelEdit();
    }
    public function render()
    {
        $todos = Todo::latest()->where('name', 'like', "%$this->search%")->paginate(3);
        return view('livewire.todo-list',compact('todos'));
    }
}
