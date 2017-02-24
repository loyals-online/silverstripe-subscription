<% if $ClassName != 'NewsletterPage' %>
    <% if not $SubscriptionSaved %>
        <span class="error-hide"></span>
        $NewsletterForm('Footer')
    <% else %>
        $SiteConfig.NewsletterThanksContent
    <% end_if %>
<% end_if %>