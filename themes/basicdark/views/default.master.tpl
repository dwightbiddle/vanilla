<!DOCTYPE html>
<html>
<head>
  {asset name="Head"}
</head>
<body id="{$BodyID}" class="{$BodyClass}">
   <div id="Frame">
      <div class="Head" id="Head">
         <div class="Row">
            <strong class="SiteTitle"><a href="{link path="/"}">{logo}</a></strong>
            <div class="SiteSearch">{searchbox}</div>
            <ul class="SiteMenu">
               <!-- {dashboard_link} -->
               {discussions_link}
               {activity_link}
               <!-- {inbox_link} -->
               {custom_menu}
               <!-- {profile_link}
               {signinout_link}  -->
            </ul>
         </div>
      </div>
      <div id="Body">
         <div class="Row">
            <div class="BreadcrumbsWrapper">{breadcrumbs}</div>
            <div class="Column PanelColumn" id="Panel">
               {module name="MeModule"}
               {asset name="Panel"}
            </div>
            <div class="Column ContentColumn" id="Content">{asset name="Content"}</div>
         </div>
      </div>
      <div id="Foot">
         <div class="Row">
            Powered by <a href="{vanillaurl}" title="Community Software by Vanilla Forums">Vanilla Forums</a> and using the <a href="http://vanillaforums.org/addon/basicdark-theme">Basic Dark theme</a> by Luciano Fiore
            {asset name="Foot"}
         </div>
      </div>
   </div>
   {event name="AfterBody"}
</body>
</html>