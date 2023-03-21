<?php
# Imports
use GDO\Gallery\GDO_GalleryImage;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Paragraph;

# Variables
/** @var $image GDO_GalleryImage * */
$gallery = $image->getGallery();
$user = $gallery->getCreator();

# Title and creator
$card = GDT_Card::make()->addClass('gdo-gallery-image')->gdo($image);
$card->creatorHeader();

$card->titleRaw($gallery->getTitle());
$card->subtitle('gallery_subtitle', [
	$gallery->getImageCount(), $user->renderUserName()]);
$card->addField(GDT_Paragraph::make()->textRaw($gallery->getMessage()));

# Image content
$html = <<<EOF
<a href="{$image->href_full()}" target="_blank">
<img src="{$image->href_show()}" alt="Gallery Image" />
</a>
EOF;
$card->addField(GDT_HTML::make()->var($html));

# Description footer
if ($image->hasDescription())
{
	# This is the power of GDO. just re-use the GDO GDT.
	$card->addField($image->gdoColumn('files_description'));
}

# Render
echo $card->render();
