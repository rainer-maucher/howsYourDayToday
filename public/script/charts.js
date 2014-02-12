/*
	Whenever you'll sit in a rabbit hole, don't forget about the charts!

	With this skill, you can confuse other, to have better escape possibilities.
	Best option, of course, is never to follow the white rabbit!
*/


var HowsYourDayToday = HowsYourDayToday || {};


/**
 * Doing all the related stuff to charts
 *
 * @constructor
 */
HowsYourDayToday.Charts = function(_user)
{
	var _requestUrl = 'request.php';
	var _chartData = [];
	var that = this;

	/**
	 * Init retrieving and rendering data for charts
	 */
	this.draw = function()
	{
		_registerEventHandler();

		_getChartDataPie();

		_getChartDataHistory();
	};

	/**
	 * Redraw charts on saved mood
	 *
	 * @private
	 */
	function _registerEventHandler()
	{
		jQuery(document).on('moodSaved', function(event, param) {
			that.draw();
		});
	}

	/**
	 * Render pie chart for mood today
	 *
	 * @param data
	 * @private
	 */
	function _drawMoodTodayPie(data)
	{
		var plot = jQuery.jqplot('moodPie', [data],
			{
				title: 'Mood today',

				seriesDefaults: {
					// Make this a pie chart.
					renderer: jQuery.jqplot.PieRenderer,
					rendererOptions: {
						sliceMargin: 4,
						// Put data labels on the pie slices.
						// By default, labels show the percentage of the slice.
						showDataLabels: true
					}
				},
				legend: { show:true, location: 'e' }
			}
		);
	}

	/**
	 * Render history chart
	 *
	 * @param data
	 * @private
	 */
	function _drawMoodHistory(data)
	{
		var plot = $.jqplot ('moodHistory', [data], {
								// Give the plot a title.
								title: 'Mood History (last 30 days)',
								seriesDefaults: {

									markerOptions: {
										size: 6
									}
								}
		});
	}

	/**
	 * Get data for mood today, to render in pie chart
	 *
	 * @private
	 */
	function _getChartDataPie()
	{
		var jqxhr = $.getJSON(_requestUrl, _getChartDataAction(), function (data) {
			_drawMoodTodayPie(data);
		})
			.fail(function () {
				alert("error on retrieving chart data!");
			});
	}

	/**
	 * Get data for mood history, to render in line chart
	 *
	 * @private
	 */
	function _getChartDataHistory()
	{
		var jqxhr = $.getJSON(_requestUrl, _getChartDataHistoryAction(), function (data) {
			_drawMoodHistory(data);
		})
			.fail(function () {
				alert("error on retrieving chart data!");
			});
	}

	/**
	 * URL and params to request data for chart
	 *
	 * @private
	 */
	function _getChartDataAction()
	{
		return 'action=getChartData&user=' + _user;
	}

	/**
	 * URL and params to request data for chart
	 *
	 * @private
	 */
	function _getChartDataHistoryAction()
	{
		return 'action=getChartDataMoodHistory&user=' + _user;
	}
};