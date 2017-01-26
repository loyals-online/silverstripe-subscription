<div id="$HolderID" class="small-6 columns column-holder field<% if $extraClass %> $extraClass<% end_if %>">
	<% if $Title %><label for="$ID">$Title</label><% end_if %>
	$Field
	<% if $Required %>
		<small class="error"><%t Form.FIELDISREQUIRED '{name} is required' name=$Title %></small>
	<% end_if %>
	<% if $RightTitle %><label class="right" for="$ID">$RightTitle</label><% end_if %>
	<% if $Message %><span class="message $MessageType">$Message</span><% end_if %>
	<% if $Description %><span class="description">$Description</span><% end_if %>
</div>
