<?php

namespace App\Http\Controllers;

use App\VKModel;
use Illuminate\Http\Request;

class VKController extends Controller
{
    public function index()
    {
    	$VKM = new VKModel();
		dd($VKM->getTopRatedWall('598731'));
    	//TODO
    }

    public function FriendList()
    {
    	//TODO
	    $VKM = new VKModel();

	    $data = array(
	    	'title'   => 'Список друзей',
		    'Owner'   => $VKM->getIntro(),
		    'FriendList' => $VKM->getFriendList()
	    );
	    dd($data);
	    return view('pages.friends',$data);
    }

    public function getUser($id)
    {
    	//TODO
    }
}
