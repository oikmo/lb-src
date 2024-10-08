<?php
	
	include('dbcon.php');
	
	$total_query = mysqli_query($con, "SELECT * FROM users");
	$total_rows = mysqli_num_rows($total_query);
	
	$raw_date = new DateTime(null, new DateTimeZone('Europe/London'));
	$date = $raw_date->format('Y/m/d');
	
	$user = "Anonymous person";
	
	if(isset($_SESSION['auth_user'])) {
		$user = $_SESSION['auth_user']['username'];
	}
	
	$splashes = [
		"This splash is sponsored by epic",
		"blame spek --space",
		"Check out <a href='https://roblogs.net'>Roblogs</a>!",
		"Do I look like I know what I'm doing?",
		"Your username IS the edit button!",
		"so retro website....",
		"the retro is so powerfull!11!1!!",
		"lock your doors if you mention skibidi toliet ( ͡° ͜ʖ ͡°)",
		"Never stop loving Contamination <3",
		"Will we make history? ... Probably not.",
		"Is this secure? ... Probably not.",
		"There are $total_rows users registered from $date!",
		"i am a contributor of climate change. --fardman84",
		"Alone... at the edge of a universe... humming a tune...",
		"See how the serfs work the grounds? (See how they FALL.)",
		"i r teh pwnz0r",
		"One secondary smile! ... To go that extra mile!!!",
		"Live, Laugh, Blame Spek",
		"i r teh pwnz0r",
		"MY ROLFCOPTER GOES SOISOISOI",
		"You are going to spontaneously combust.",
		"Temu of Revivals",
		"Lambda? What's that?",
		"How to uhhh uhhhhh",
		"high ground? we below the ground, we are the fish 🐟",
		"Lambdablox? nah Half blox.",
		"Such a shame there is only one client...",
		"Forever BLAME SPEK!!",
		"$user my beloved",
		"GO WATCH FLCL PLEASE",
		"Neon Genesis Evangelion... goated much?",
        "JERMA JERMA JERMA MY BELOVED",
        "Sparkle on! Don't forget to be yourself!",
        "LIFE IS PAIN I HATE-",
		"Rise and shine, Mr. Freeman...",
		"Rise and shine and smell the ashes...",
		"jayjoke was here 😭",
	];
?>