<% if $ClassName != 'NewsletterPage' %>
    <% if not $SubscriptionSaved %>
        $NewsletterForm('Footer')
    <% else %>
        $SiteConfig.NewsletterThanksContent
    <% end_if %>
<% end_if %>