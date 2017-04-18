<?php
	if (!defined('ABSPATH'))
	    exit; // Exit if accessed directly

	$btwbSettings = get_option(BTWB_SETTINGS_OPTION, 0);
	$btwbSettingsObj = json_decode($btwbSettings);
?>
<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" crossorigin="anonymous" />
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
		<form role="form" class="form" style="margin-top:20px;">
			<table class="table">
			<tr class="form-group">
				<td style="border: none; padding: 3px 0;"><label>Section(s)</label></td>
				<td style="border: none; padding: 3px 0;" colspan="2">
					<label class="checkbox-inline"><input type="checkbox" class="section-checkbox" value="pre">Pre</label>
					<label class="checkbox-inline"><input type="checkbox" class="section-checkbox" value="post">Post</label>
					<label class="checkbox-inline"><input type="checkbox" class="section-checkbox" value="main">Main</label>
					<label class="checkbox-inline"><input type="checkbox" class="section-checkbox" value="all">All</label>
				</td>
			</tr>
			<tr class="form-group">
				<td style="border: none; padding: 3px 0;"><label>Date</label></td>
				<td colspan="2" style="border: none; padding: 3px 0;">
					<input class="form-control datepicker" type="text" id="wod_date">
					<p class="small">Leave Blank to always show the current day</p>
				</td>
			</tr>
			<tr>
				<td style="border: none; padding: 3px 0;"><label>Track(s)</label></td>
				<td colspan="2" style="border: none; padding: 3px 0;">
					<select class="form-control" id="wod_tracks" multiple>
						<?php
							if(!empty($btwbSettingsObj->tracks)){
								foreach($btwbSettingsObj->tracks as $btwbWodTrackId => $btwbWodTrack){
									echo "<option value=\"{$btwbWodTrackId}\">{$btwbWodTrack->name}</option>";
								}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="border: none; padding: 3px 0;"><label>Group</label></td>
				<td colspan="2" style="border: none; padding: 3px 0;">
					<select class="form-control" id="wod_group">
						<?php
							if(!empty($btwbSettingsObj->member_lists)){
								foreach($btwbSettingsObj->member_lists as $btwbMemberListId => $btwbMemberList){
									echo "<option value=\"{$btwbMemberListId}\">{$btwbMemberList->name}</option>";
								}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="border: none; padding: 3px 0;"><label>Activity Length</label></td>
				<td colspan="2" style="border: none; padding: 3px 0;">
					<select class="form-control" id="wod_activity_length">
						<option value="">Default Length</option>
						<option value="0">Hide Activity</option>
						<?php
							for($activityLenghtCounter = 1; $activityLenghtCounter <= 30; $activityLenghtCounter++){
								echo "<option value=\"{$activityLenghtCounter}\">{$activityLenghtCounter}</option>";
							}
						?>
					</select>
				</td>
			</tr>

			</tr>
			<tr>
				<td style="border: none; padding: 3px 0;"><label>Leaderboard Length</label></td>
				<td colspan="2" style="border: none; padding: 3px 0;">
					<select class="form-control" id="wod_leaderboard_length">
						<option value="">Default Length</option>
						<option value="0">Hide Leaderboard</option>
						<?php
							for($leaderboardLenghtCounter = 1; $leaderboardLenghtCounter <= 20; $leaderboardLenghtCounter++){
								echo "<option value=\"{$leaderboardLenghtCounter}\">{$leaderboardLenghtCounter}</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="border: none; padding: 3px 0;"><label>Show Previous Days</label></td>
				<td colspan="2" style="border: none; padding: 3px 0;">
					<select class="form-control" id="wod_days">
						<option value="">No Previous Days</option>
						<?php
							for($dayLenghtCounter = 1; $dayLenghtCounter <= 20; $dayLenghtCounter++){
								echo "<option value=\"{$dayLenghtCounter}\">{$dayLenghtCounter}</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="border: none; padding: 3px 0;">
					<input type="checkbox" name="" id="wod_privacy" class="btwb-margin-top-15" value="1" />
					<label>Include results with "Gym/Coaching only" privacy level?</label>
					<p>(Only select this if you are putting this on a protected page)</p>
				</td>
			</tr>
		</table>
		</form>
		</div>
	</div>
</div>
<script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js" crossorigin="anonymous"></script>
<script>
	$(window).on('load', function() {
		$('.datepicker').datepicker({ format: 'yyyy-mm-dd',});
	});
</script>
</body>
</html>
