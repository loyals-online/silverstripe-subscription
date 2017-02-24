/**
 * Created by jpvanderpoel on 24/02/2017.
 */
(function ($) {
    $(document).ready(function () {
        $("#NewsletterForm_NewsletterForm").submit(function (e) {
            e.preventDefault();
            var $this = $(this);
            $.post($this.attr("action"),$this.find("input"), function(data, textStatus, jqXHR){
                var response = JSend.parse(data);
                $('#response').remove();
                if(response.isSuccess()){
                    if(response.hasData()){
                        $this.replaceWith(response.getData().response);
                    }
                } else {
                    $this.parent().find("span").text(response.getData().response).attr('class', 'error');
                }
            });
        });
    });
}(jQuery));
