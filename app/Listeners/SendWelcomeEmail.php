<?php
	
	namespace App\Listeners;
	
	use App\Events\UserRegistered;
	use Illuminate\Contracts\Queue\ShouldQueue;
	use Illuminate\Queue\InteractsWithQueue;
	//use App\Mail\WelcomeMail;
	use Illuminate\Support\Facades\Mail;
	
	class SendWelcomeEmail
	{
		/**
			* Create the event listener.
			*
			* @return void
		*/
		public function __construct()
		{
			//
		}
		
		/**
			* Handle the event.
			*
			* @param  \App\Events\UserRegistered  $event
			* @return void
		*/
		public function handle(UserRegistered $event)
		{
			$name = $event->user->first_name;
			$email = $event->user->email;
			Mail::send('emails.welcome', ['name' => $name, 'email' => $email], function ($message) use ($email)
			{
				$message->from($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
				$message->to($email);
				$message->subject('WelCome Mail');
			});
			
			
			//Mail::to($event->user->email)->send(new WelcomeMail($event->user));
		}
	}
