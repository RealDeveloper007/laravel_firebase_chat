<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chat;

class ChatController extends Controller
{

	public function SendMessage(Request $request)
	{
		$this->validate($request, [
			'to_id'   => 'required',
			'body'    => 'required',
		]);

		$ChatModel = new Chat();
		$ChatModel->to_id = $request->to_id;
		$ChatModel->from_id = \Auth::User()->id;
		$ChatModel->body = $request->body;
		$ChatModel->date = date('Y-m-d');
		$ChatModel->time = date('H:i:s');
		$ChatModel->save();

		$data['time'] = date('H:i:s');
		$data['date'] = $this->get_day_name(date('Y-m-d'));
		return response()->json($data);
		// return response(['data' => $chat], 200);

	}

	// Get Date Format
	private function get_day_name($getdate)
	{

		$Today = date('Y-m-d');

		$Yesterday = date('Y-m-d', strtotime('-1 day', strtotime($Today)));

		if ($getdate == $Today) {
			$date = 'Today';
		} else if ($getdate == $Yesterday) {
			$date = 'Yesterday';
		} else {
			$date = date('d-M-Y', strtotime($getdate));
		}
		return $date;
	}
}
