=== Plugin Name ===
Contributors: zjhzxhz
Donate link: https://haozhexie.com/
Tags: BibTeX, citation, paper
Requires at least: 3.0.1
Tested up to: 6.4.3
Stable tag: 3.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This WordPress plugin provides a way to showcase your publications, along with optional featured images and the corresponding BibTeX text.

== Description ==

The plugin allows users to generate a bibliography-style text for a publication using the provided information.
To use the plugin, users can insert a shortcode into a page with the relevant publication details, such as the title, author, journal, year, pages, publisher, volume, URL, and note. 

For example, the shortcode `[WpBibTeX type="inproceedings" title="Efficient Regional Memory Network for Video Object Segmentation" author="Xie, Haozhe and Yao, Hongxun and Zhou, Shangchen and Zhang, Shengping and Sun, Wenxiu" booktitle="CVPR" year="2021" paper="https://arxiv.org/pdf/2103.12934.pdf" projectpage="//infinitescript.com/project/rmnet/"]` generates the bibliography-style text 
`Haozhe Xie, Hongxun Yao, Shangchen Zhou, Shengping Zhang, Wenxiu Sun. Efficient Regional Memory Network for Video Object Segmentation. CVPR, 2021.` as well as the BibTeX cite code below. 

`@inproceedings{xie2021efficient,
  title={Efficient Regional Memory Network for Video Object Segmentation},
  author={Xie, Haozhe and Yao, Hongxun and Zhou, Shangchen and Zhang, Shengping and Sun, Wenxiu},
  booktitle={CVPR},
  year={2021}
}`

Users can also customize the links displayed on the page, such as adding a new link for downloading code.

**New Features in 3.x**

Starting from WP-BibTeX 3.0, it is now possible to create a WP-BibTeX item with a featured image on the left using the new "highlighted" format.
This can be achieved by adding the img attribute to the existing shortcode. For example: `[WpBibTeX type="inproceedings" title="..." author="..." booktitle="CVPR" year="2021" img="/url/to/img"]`

Additionally, you can create an overlaid media (such as a GIF or video) when the mouse is hovering by adding the overlay attribute to the shortcode. To ensure compatibility with all devices, you can create a fallback media using the `overlay_fallback` attribute. 
Here is an example of the complete shortcode: 

`[WpBibTeX type="inproceedings" title="Efficient Regional Memory Network for Video Object Segmentation" author="Xie, Haozhe and Yao, Hongxun and Zhou, Shangchen and Zhang, Shengping and Sun, Wenxiu" booktitle="CVPR" year="2021" paper="https://arxiv.org/pdf/2103.12934.pdf" projectpage="//infinitescript.com/project/rmnet/" img="https://shangchenzhou.com/assets/img/papers/Conf2021_rmnet.jpg" overlay="//infinitescript.com/wordpress/wp-content/uploads/highlights/research-rmnet.webm" overlay_fallback="//infinitescript.com/wordpress/wp-content/uploads/highlights/research-rmnet.gif"]`.

**New Features in 2.x**

Additionally, it is possible to personalize the links shown on the page. By default, each citation has two links, namely `[BibTeX]` and `[Download PDF]`. However, it is now possible to include custom links by accessing the option page.

Suppose a new field is created with a Field Key of code and a Field Name of Download Code. The code attribute is then used in the `[WpBibTeX]` shortcode as follows:

`[WpBibTeX type="article" ... code="https://link/to/the/code"]`

After adding this shortcode, a new link, `[Download Code]`, will appear on the page after the `[BibTeX]` link.

**Supported BibTeX Entry Types**

Currently, this plugin support following BibTeX entries types:

*article*

- **Required fields:** `type`, `title`, `author`, `journal`, `year`
- **Optional fields:** `volume`, `number`, `pages`, `month`, `publisher`, `url`, `note`
- **Short code example:** `[WpBibTeX type="article" title="Title of the citation" author="First Name1, Last Name1 and First Name2, Last Name2 and First Name3, Last Name3" journal="Journal Name of the citation" year="2016" volumne="1" url="The download link of the article" note="(IF=1.000)"]`

*book*

- **Required fields:** `type`, `title`, `author`, `publisher`, `year`
- **Optional fields:** `volume`, `number`, `series`, `edition`, `month`, `address`, `isbn`, `url`, `note`
- **Short code example:** `[WpBibTeX type="book" title="Title of the citation" author="First Name1, Last Name1 and First Name2, Last Name2 and First Name3, Last Name3" publisher="Publisher of the citation" year="2016" address="The address of the publisher" url="The download link of the article"]`

*inproceedings*

- **Required fields:** `title`, `author`, `booktitle`, `year`
- **Optional fields:** `volume`, `number`, `series`, `pages`, `month`, `organization`, `publisher`, `address`, `url`, `note`
- **Short code example:** `[WpBibTeX type="inproceedings" title="Title of the citation" author="First Name1, Last Name1 and First Name2, Last Name2 and First Name3, Last Name3" booktitle="The name of the conference, such as CVPR" address="The address of the publisher" year="2016" note='<span style="color: red">write anything you want</span>']`

*mastersthesis*

- **Required fields:** `title`, `author`, `school`, `year`
- **Optional fields:** `month`, `address`, `url`, `note`
- **Short code example:** `[WpBibTeX type="mastersthesis" title="Title of the citation" author="First Name1, Last Name1" school="The place where the citation was written" year="2016"]`

*phdthesis*

- **Required fields:** `title`, `author`, `school`, `year`
- **Optional fields:** `month`, `address`, `url`, `note`
- **Short code example:** `[WpBibTeX type="phdthesis" title="Title of the citation" author="First Name1, Last Name1" school="The place where the citation was written" year="2016"]`

*unpublished*

- **Required fields:** `title`, `author`
- **Optional fields:** `month`, `year`, `url`, `note`
- **Short code example:** `[WpBibTeX type="unpublished" title="Title of the citation" author="First Name1, Last Name1"]`

== Installation ==

1. Upload `WP-BibTeX` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enjoy!

== Frequently Asked Questions ==

1. Who need this plugin?

Researchers who need to display their publications in their homepage can use this plugin to manage and list their works easier.

== Screenshots ==

1. Output Preview
2. Options Page

== Changelog ==

= 3.0.1 =
* Fix the bugs causing non-UTF-8 author names cannot display correctly.

= 3.0.0 =
* Support display highlight publications which allows to display an image for a publication.

= 2.2.7 =
* Fix some bugs that cause WordPress fatal errors.

= 2.2.6 =
* Fix the bug when generating Ph.D. thesis BibTeX items.

= 2.2.5 =
* Fix some bugs.

= 2.2.4 =
* Support arXiv publications.

= 2.2.3 =
* Fix bugs causing update failed.

= 2.2.2 =
* Open link in a new window.
* Remove `impact_factor` field for journal articles.
* Don't display `note` field in BibTeX.

= 2.2.1 =
* Fix bugs in bibliography style text.

= 2.2.0 =
* Allow use HTML tag in `note` field.

= 2.1.0 =
* Add field `note` for add custom text for citations.

= 2.0.1 =
* Fix bugs for PHP version lower than 7.

= 2.0.0 =
* Support customize fields for citations and these fields will be displayed as a link in the page.

= 1.1.0 =
* Highlight the blog owner himself/herself in bibiography text.

= 1.0.0 =
* Complete basic function for convert citation information to BibTex and bibiography style text.
