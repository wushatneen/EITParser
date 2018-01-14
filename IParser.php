<?php
interface IParser {
	function getMainImage($id);
	function getTitle($id);
	function getDescription($id);
	function getAuthor($id);
	function getDate($id);
	function getCategory($id);
	function getLikes($id);
	function getDislikes($id);
	function getComments($id);
	function getViews($id);
	function getLink($id);
	function getFullText($link);
	function getBest($id);
}