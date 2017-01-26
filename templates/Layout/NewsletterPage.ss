<section class="content">
    <div class="row">

        <% include Submenu %>
        <div class="medium-8 <% if not $Menu(2) %>small-centered<% end_if %> columns">
            <% if not $SubscriptionSaved %>
                <h1>$Title</h1>
                $Content

                $NewsletterForm
            <% else %>
                <h1>$SiteConfig.NewsletterThanksTitle</h1>
                $SiteConfig.NewsletterThanksContent
            <% end_if %>
        </div>

    </div>
</section>