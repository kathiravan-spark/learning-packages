<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\MailJob;
use Illuminate\Support\Facades\DB;
  
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(User $user)
    {
        $this->user=$user;     
    }
    public function index(Request $request)
    {
        $users=User::select('*')->get();
        foreach($users as $user){
           $user_email = $user->email;
           $mail= substr($user_email,-14);
           if($mail != 'mailinator.com'){ 
            $updated_email =  str_replace( $mail, '@mailinator.com', $user_email);
            $users = User::where('email',$user_email)->update(['email'=>$updated_email]);  
           }
        }
        if($request->filled('search')){
            $users = User::search($request->search)->get();
        }else{
            $users = User::get();
        }
        $jobs = new MailJob($user);
            dispatch($jobs)->onQueue('email');
        return view('users', compact('users'));
    }
}