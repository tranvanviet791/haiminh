jQuery(function($){

    /**
     *  Re-calculate Sales Amount of a product
     */
    $('.recal-sa').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $(this).attr('rel');
        $this.html("<i class='fa fa-spinner fa-spin'></i>");
        $.post(ajaxurl, {action: 'RecalculateSales', id: $(this).attr('rel')}, function(res){
            $this.html(res.sales_amount);
            $('#sc-'+id).html(res.sales_quantity);
        });
    });


    /**
     *  Settings >> Premium Package >> Basic Settings
     */

    $('body').on('click', '#allowed_cn', function () {
        $('.ccb').prop('checked', this.checked);
    });

    /**
     *  Settings >> Premium Package >> Tax
     */

    jQuery('.taxstate,.taxcountry').chosen({width:'200px'});

    jQuery('.taxcountry').on('change', function(){
        var row_id = jQuery(this).attr('rel');
        WpdmppPopulateStates(row_id);
    });

});


// For Adding New Tax Rate
function populateCountryStateAdmin(row_id) {
    var $ = jQuery;

    var dataurl = wpdmpp_base_url + 'assets/js/data/';

    var countries = [], states = [], countryOptions ="",  stateOptions ="", countrySelect = $('#r_'+ row_id +' .taxcountry'), stateSelect = $('#r_'+ row_id +' .taxstate');

    $.getJSON(dataurl+'countries.json', function(data){
        $.each(data, function(i, country){
            countries[""+country.code] = country.filename;
            countryOptions += "<option value='"+country.code+"'>"+country.name+"</option>";
        });
        countrySelect.html(countryOptions);
        countrySelect.chosen();
    });
    countrySelect.change(function() {
        var countryCode = $(this).val();
        loadStates(countryCode);
    });

    function loadStates(countryCode){
        console.log('populateCountryStateAdmin loadStates');
        var filename = countries[countryCode];
        if(filename != undefined) {
            $('#r_' + row_id + ' .taxstate-text').attr('disabled','disabled').hide();
            stateSelect.removeAttr('disabled').show();
            $.getJSON(dataurl + 'countries/' + filename + '.json', function (data) {
                stateOptions = "";
                stateOptions += "<option value='ALL-STATES'>All States</option>";
                $.each(data, function (i, state) {
                    states["" + state.code] = state;
                    var scode = state.code.replace(countryCode + "-", "");
                    stateOptions += "<option value='" + scode + "'>" + state.name + "</option>";
                });
                stateSelect.html(stateOptions).chosen().addClass('hidden').trigger("chosen:updated");
            });
        } else {
            stateSelect.attr('disabled','disabled').hide();
            $('#states_'+row_id+' .chosen-container').addClass('chosen-disabled');
            $('#r_' + row_id + ' .taxstate-text').removeAttr('disabled').show();
        }

    }
}

// For Updating Old Tax Rate
function WpdmppPopulateStates(row_id) {
    var $ = jQuery;

    var dataurl = wpdmpp_base_url + 'assets/js/data/';

    var countries = [], states = [], countryOptions ="",  stateOptions ="", countrySelect = $('#r_'+ row_id +' .taxcountry'), stateSelect = $('#r_'+ row_id +' .taxstate');

    $.getJSON(dataurl+'countries.json', function(data){
        $.each(data, function(i, country){
            countries[""+country.code] = country.filename;
        });

        var countryCode = countrySelect.val();
        loadStates(countryCode);
    });


    function loadStates(countryCode){
        console.log('populateStates loadStates');
        var filename = countries[""+countryCode];
        if(filename != undefined) {
            $('#r_' + row_id + ' .taxstate-text').attr('disabled','disabled').hide();
            stateSelect.removeAttr('disabled').show();
            $.getJSON(dataurl + 'countries/' + filename + '.json', function (data) {
                stateOptions = "";
                stateOptions += "<option value='ALL-STATES'>All States</option>";
                $.each(data, function (i, state) {
                    states["" + state.code] = state;
                    var scode = state.code.replace(countryCode + "-", "");
                    stateOptions += "<option value='" + scode + "'>" + state.name + "</option>";
                });

                stateSelect.html(stateOptions).addClass('hidden').trigger("chosen:updated");
            });
        } else {
            stateSelect.attr('disabled','disabled').hide();
            $('#cahngestates_'+row_id+' .chosen-container').addClass('chosen-disabled');
            $('#r_' + row_id + ' .taxstate-text').removeAttr('disabled').show();
        }

    }
}