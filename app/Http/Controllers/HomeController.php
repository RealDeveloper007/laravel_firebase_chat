<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Chat;
use Auth;

class HomeController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth');
	}
	
	public function index()
    {
        $Users = User::where('id', '<>', Auth::User()->id)->get()->toArray();

        $UserChats = array();
        $i = 0;
        foreach ($Users as $AllUserChats) {
            $UserChats[$i]                    = $AllUserChats;
            $UserChats[$i]['messages']        = $this->TwoUserMessages($AllUserChats['id']);
            $i++;
        }

        return view('chat.index', ['users' => $Users, 'userchats' => $UserChats]);
    }

    public function ExportCsv(Request $request)
	{
		$this->validate($request, [
			'chat_id'   => 'required',
		]);


		$SessionUser = Auth::User()->id;
        $UserId = $request->chat_id;
        
        // echo $UserId; die;

		$fileName = $UserId.'_chats.csv';


        $ChatMessages = Chat::where(function($query) use ($UserId,$SessionUser){
                    $query->where('from_id','=',$UserId)
                   ->Where('to_id','=',$SessionUser);
               })
              ->orWhere(function($query2) use ($UserId,$SessionUser){
                    $query2->where('from_id','=',$SessionUser)
                   ->where('to_id','=',$UserId);

        })->get();

		$headers = array(
			"Content-type"        => "text/csv",
			"Content-Disposition" => "attachment; filename=$fileName",
			"Pragma"              => "no-cache",
			"Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
			"Expires"             => "0"
		);

		$columns = array('From', 'To', 'Message', 'Date', 'Time');

		$row = array();

		$callback = function () use ($ChatMessages, $columns) 
		{
			$file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            


			$i = 0;
			foreach ($ChatMessages as $Chats) 
			{
				$row[$i]['from']           =  $Chats->fromdetails->name;
				$row[$i]['to']             =  $Chats->todetails->name;
				$row[$i]['body']           =  $Chats->body;
				$row[$i]['date']           =  date('d,M', strtotime($Chats->date));
				$row[$i]['time']           =  $Chats->time;

			fputcsv($file, array($row[$i]['from'], $row[$i]['to'], $row[$i]['body'], $row[$i]['date'], $row[$i]['time']));

				$i++;
            }
            
            fclose($file);
            

        };
        
				
		return response()->stream($callback, 200, $headers);
	}

    // Find the  messages between two users
    private function TwoUserMessages($UserId)
    {
        $SessionUser = Auth::User()->id;
        
        $ChatMessages = Chat::where(function($query) use ($UserId,$SessionUser){
            $query->where('from_id','=',$UserId)
           ->Where('to_id','=',$SessionUser);
       })
      ->orWhere(function($query2) use ($UserId,$SessionUser){
            $query2->where('from_id','=',$SessionUser)
           ->where('to_id','=',$UserId);

        })->get();


        $AllMessges = array();
        $i = 0;
        foreach ($ChatMessages as $Chats) {
            $AllMessges[$i]['id']             =  $Chats->id;
            $AllMessges[$i]['from']           =  $Chats->fromdetails->name;
            $AllMessges[$i]['from_id']        =  $Chats->from_id;
            $AllMessges[$i]['from_image']     =  $Chats->fromdetails->profile_img;
            $AllMessges[$i]['to']             =  $Chats->todetails->name;
            $AllMessges[$i]['to_id']          =  $Chats->to_id;
            $AllMessges[$i]['to_image']       =  $Chats->todetails->profile_img;
            $AllMessges[$i]['body']           =  $Chats->body;
            $AllMessges[$i]['date']           =  date('d,M', strtotime($Chats->date));
            $AllMessges[$i]['time']           =  $Chats->time;

            $i++;
        }

        return $AllMessges;
    }
}
