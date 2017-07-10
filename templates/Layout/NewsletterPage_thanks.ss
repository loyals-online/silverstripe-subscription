<section class="content">
    <div class="row">

        <% include Submenu %>
        <div class="medium-8 <% if not $Menu(2) %>small-centered<% end_if %> columns">
            <h1>$SiteConfig.NewsletterThanksTitle</h1>
            $SiteConfig.NewsletterThanksContent
        </div>

    </div>
</section>