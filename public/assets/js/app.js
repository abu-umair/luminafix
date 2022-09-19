$(function () {
    $(".form-group.has-widgets").each(function () {
        const widget = $(this).find(".password-visibility-widget");
        widget.on("click", function (event) {
            event.preventDefault();
            const icon = $(this).find("i.fas");
            const input = $($(this).data("target"));

            if (!input || !icon) return;

            // Remove all classes
            icon.removeClass("fa-eye fa-eye-slash");

            const visible = input.attr("type") !== "password";

            if (visible) {
                input.attr("type", "password");
                icon.addClass("fa-eye");
                $(this).attr("title", "Show password");
            } else {
                input.attr("type", "text");
                icon.addClass("fa-eye-slash");
                $(this).attr("title", "Hide password");
            }
        });
    });
});
