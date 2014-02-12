/**
 * Doing all the required js stuff to make site working
 * @param _user
 * @constructor
 */
HowsYourDayToday = function(_user)
{
	var that = this;
	var _DEFAULT_MOOD = 3;
	var _requestUrl = "/request.php";

	/**
	 * Initializes State of Slider and poplate site with data
	 */
	this.init = function()
	{
		var jqxhr = $.getJSON(_requestUrl, _getGetPageDataAction(), function (data) {
			_populatePage(data);
		})
		.fail(function () {
			alert("error");
		});
	};

	/**
	 * populates page with data
	 * @param data
	 * @private
	 */
	function _populatePage(data)
	{
		var actuallMood = _DEFAULT_MOOD;
		if (data && data.user && data.user.mood) {
			actuallMood = data.user.mood;
		}

		$("#minbeds option[value='" + actuallMood + "']").attr('selected', true);
		// trigger change event so slider redraws
		$("#minbeds").trigger('change');

		if (data['data']['averageMood']) {
			$text = $("#minbeds option[value='" + data['data']['averageMood'] + "']").html();
			$('#companyAverageMood span').text($text);
			$('#moodBox').removeClass('hidden');
			$('#companyAverageMood').removeClass('hidden');
		}

		if (data['data']['count']) {
			$('#peopleMooded span').text(data['data']['count']);
			$('#moodBox').removeClass('hidden');
			$('#peopleMooded').removeClass('hidden');
		}
	}

	/**
	 * initializes slider
	 */
	this.drawSlider = function() {
		var select = $("#minbeds");
		var slider = $("<div id='slider'></div>").insertAfter(select).slider({
			min: 1,
			max: 6,
			range: "min",
			value: select[ 0 ].selectedIndex + 1,
			slide: function (event, ui) {
				select[ 0 ].selectedIndex = ui.value - 1;
			}
		});

		$("#minbeds").change(function () {
			slider.slider("value", this.selectedIndex + 1);
		});
	};

	/**
	 * submits mood to server
	 */
	this.sendMood = function()
	{
		var jqxhr = $.get(_requestUrl, _getSendMoodData(), function () {

		})
			.success(function () {
				// redraw page
				that.init();

				// Fire custom event allow others to add event
				// handler for this, add mood data as param:
				jQuery.event.trigger('moodSaved', _getSendMoodData());
				alert("Your mood has been saved. Feel free to change your mood everytime it gets better or worse.");
			})
			.fail(function () {
				alert("error");
			})
	};

	/**
	 * Collects data for request we want to submit
	 * @private
	 */
	function _getSendMoodData()
	{
		return {'action': 'addMood', 'mood': $('#minbeds').val(), "user": _user}
	}

	/**
	 * Collects data for request we want to submit
	 * @private
	 */
	function _getGetPageDataAction()
	{
		return {'action': 'getPageData', "user": _user}
	}
};