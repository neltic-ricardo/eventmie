<?php

namespace App\Http\Controllers\Eventmie;

use Classiebit\Eventmie\Http\Controllers\ProfileController as BaseProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Classiebit\Eventmie\Models\User;
use Facades\Classiebit\Eventmie\Eventmie;
use Illuminate\Support\Carbon;

use Classiebit\Eventmie\Notifications\MailNotification;
use Auth;

use Intervention\Image\Facades\Image;
use File;

class ProfileController extends BaseProfileController
{    
    /**
     * index
     *
     * @param  String $view
     * @param  Array $extra
     * @return view
     */
    public function index($view = 'vendor.eventmie-pro.profile.profile', $extra = [])
    {
        return parent::index($view, $extra);
    }

    /**
     * updateOrganiser
     *
     * @param   Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function updateSeller(Request $request)
    {
        // demo mode restrictions
        if(config('voyager.demo_mode'))
        {
            return error_redirect('Demo mode');
        }

        
        
        $user = User::find(Auth::id());
        if(Auth::user()->hasRole('organiser')) {

            $this->uploadImage($request, $user);
            $this->sellerInfo($request, $user);

        }

        $user->save();
        // redirect no matter what so that it never turns back
        $msg = __('eventmie-pro::em.saved').' '.__('eventmie-pro::em.successfully');
        return success_redirect($msg, route('eventmie.profile').'#/userSellerInfo');

    }

    /**
     * updateOrganiser
     *
     * @param   Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function updateMailchimp(Request $request)
    {
        // demo mode restrictions
        if(config('voyager.demo_mode'))
        {
            return error_redirect('Demo mode');
        }
        $user = User::find(Auth::id());

        $this->mailchimp($request, $user);

        $user->save();

        // redirect no matter what so that it never turns back
        $msg = __('eventmie-pro::em.saved').' '.__('eventmie-pro::em.successfully');
        return success_redirect($msg, route('eventmie.profile').'#/userMailchimp');

    }

    /**
     * mailchimp
     *
     * @param   Illuminate\Http\Request $request
     * @param  App\Model\User $user
     * @return void
     */    
    protected function mailchimp(Request $request, $user = null)
    {
        $user->mailchimp_apikey      = $request->mailchimp_apikey;
        $user->mailchimp_list_id     = $request->mailchimp_list_id;
        
    }
    
    /**
     * uploadImage
     *
     * @param   Illuminate\Http\Request $request
     * @param  App\Model\User $user
     * @return void
     */
    protected function uploadImage(Request $request, User $user)
    {
        $path   = 'users/';

        // for image
        if($request->hasfile('avatar')) 
        { 
            $request->validate([
                'avatar' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            
            ]); 
        
            $file = $request->file('avatar');
    
            $extension       = $file->getClientOriginalExtension(); // getting image extension
            $avatar          = time().rand(1,988).'.'.$extension;
            $file->storeAs('public/'.$path, $avatar);
            
            $user->avatar    = $path.$avatar;
            
        }
        
        if(empty($user->avatar) || $user->avatar == 'users/default.png')
        {
            $request->validate([
                'avatar' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            
            ]); 
        }

        if(Auth::user()->hasRole('organiser')) 
        {
            if($request->hasfile('seller_signature')) 
            { 
                $request->validate([
                    'seller_signature' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                
                ]); 
            
                $file = $request->file('seller_signature');
        
                $extension       = $file->getClientOriginalExtension(); // getting image extension
                $avatar          = time().rand(1,988).'.'.$extension;
                $file->storeAs('public/'.$path, $avatar);
                
                $user->seller_signature    = $path.$avatar;
                
            }
            
            if(empty($user->seller_signature) || $user->seller_signature == 'users/default.png')
            {
                $request->validate([
                    'seller_signature' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                
                ]); 
            }

        }

    }
    
    /**
     * sellerInfo
     *
     * @param   Illuminate\Http\Request $request
     * @param  App\Model\User $user
     * @return void
     */
    protected function sellerInfo(Request $request, User $user)
    {
        $request->validate([
            'seller_name'        => 'max:256',
            'seller_info'        => 'max:256',
            'seller_tax_info'    => 'max:256',
            'seller_note'        => 'max:256',
        ]);

        $user->seller_name       = $request->seller_name;
        $user->seller_info       = $request->seller_info;
        $user->seller_tax_info   = $request->seller_tax_info;
        $user->seller_note       = $request->seller_note;
    }
    
}
