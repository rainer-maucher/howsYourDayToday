<!doctype html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title>Hows your mood today?</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="./public/css/style.css">
	<script src="./public/script/howsYourDayToday.js"></script>

</head>
<body>
<h1>How is your mood today?</h1>
<form id="reservation">
	<label for="minbeds">So, how are you?</label>
	<select name="minbeds" id="minbeds">
		<option value="1">Please Shoot me</option>
		<option value="2">Bad</option>
		<option value="3">Good</option>
		<option value="4">All Righty right</option>
		<option value="5">Great</option>
		<option value="6">Fucking Awesome</option>
	</select>
</form>
<br/>
<button onclick="howsYourDayToday.sendMood(); return false;">Send Now</button>
<p id="companyAverageMood" class="hidden">
	The companys average mood today is: <span></span>
</p>
<p id="peopleMooded" class="hidden">
	People Mooded today: <span>blubber</span>
</p>
<script type="text/javascript" >



	var howsYourDayToday;
	$(document).ready(function () {
		howsYourDayToday = new HowsYourDayToday("<?= getenv ("REMOTE_ADDR"); ?>");

		howsYourDayToday.drawSlider();
		howsYourDayToday.init();
	});


</script>
</body>
</html>