<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use App\Models\Student;
use Livewire\Component;


class StudentShow extends Component
{
    public $name, $email, $course, $student_id ;
   // public $students;

    protected $paginationTheme = 'bootstrap';
    use WithPagination;
    public $search = '';

    // Start Validate form data using Livewire
    protected function rules()
    {
        return [
            'name' => 'required|string|min:6',
            'email' => ['required', 'email'],
            'course' => 'required|string',
        ];
    }

    public function updated($fields)
    {
        $this->validateOnly($fields);
    }
    // End Validate form data using Livewire

    // Start Insert Validated data in database
    public function saveStudent()
    {
        $validatedData = $this->validate();
        
        Student::create($validatedData);
        session()->flash('message','Student Added Successfully !');
        $this->resetInput();
        $this->dispatchBrowserEvent('close-model');

    }
    // End Insert Validated data in database

    // Start Reset form data 
    public function resetInput(){
        $this->name = '';
        $this->email = '';
        $this->course = '';
    }
    // End Reset form data 

    // geting Data and Display Data in Table
    public function render()
    {
        $students = Student::where('name', 'like', '%'.$this->search.'%')->orderBy('id','DESC')->paginate(3);
        return view('livewire.student-show', ['students'=> $students]);
    }

    // Showing student data in form when click on update
    public function editStudent(int $student_id){
        $student = Student::find($student_id);
        if($student){
            $this->student_id = $student->id;
            $this->name = $student->name;
            $this->email = $student->email;
            $this->course = $student->course;
        }else{
            return redirect()->to('/students');
        }
    }

    // Update form data
    public function updateStudent(){
        $validatedData = $this->validate();

        Student::where('id',$this->student_id)->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'course' => $validatedData['course']
        ]);
        session()->flash('message','Student Update Successfully !');
        $this->resetInput();
        $this->dispatchBrowserEvent('close-model');

    }

    // reset form data at the time of closing the for 
    public function closeModal(){
        $this->resetInput();
    }

    // Delete Student data id get here
    public function deleteStudent(int $student_id){
        $this->student_id = $student_id;
    }

    // Delete data by it here
    public function destroyStudent(){
        Student::find($this->student_id)->delete();
        session()->flash('message','Student Deleted Successfully !');
        $this->dispatchBrowserEvent('close-model');
    }


// End Class    
}
