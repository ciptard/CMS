<?
function article_construct(){
	//check if user is an administrator
	mod_check();
	load_model('article');
}

function article_submit()
{
	$data['name'] = get_session('user_name');
	
	$data['pageJs'] = true;
	$data['js'] = 'submitArticle.js';
	load_helper('form');
	if(!form_submitted())
	{
		$data['errors'] = false;
		load_view('submitArticle', $data, false, true);
	}else{
		$data['errors'] = false;
		
		$data['title'] = $_POST['title'];
		if($data['title'] == "")
		{
			$formError['title'] = "Title cannot be empty.";
			$data['errors'] = true;
		}
		
		$data['category'] = $_POST['category'];
		if($data['category'] == "")
		{
			$formError['category'] = "Category cannot be empty.";
			$data['errors'] = true;
		}
		
		$data['content'] = $_POST['elm1'];
		if($data['content'] == "")
		{
			$formError['elm1'] = "Content cannot be empty.";
			$data['errors'] = true;
		}
		
		$data['tags'] = trim($_POST['tags']);
		$data['categoryId'] = $_POST['categoryId'];
		
		// If errors refresh page.
		if($data['errors'])
		{
			$data['formError'] = $formError;
			load_view('submitArticle',$data, false, true);
		}else{
			$article['title'] = $data['title'];
			$article['userId'] = user_id();
			$article['category'] = $data['categoryId'];
			$article['content'] = $data['content'];
			$tagsList = $data['tags'];
			$result = model_exec('article','submit', $article);
			if($result['revisionId'] != 0)
			{
				unset($data);
				$data['notice'] = "Article successfully submitted.";
				if($tagsList != "")
				{
					$tags = split(",",$tagsList);
					foreach($tags as $tag)
					{
						$tName = trim($tag);
						if($tName != "")
						{
							$tagName['name'] = $tName;
							$tagName['articleId'] = $result['revisionId'];
							$tagResult = model_exec('article','addTag', $tagName);
							if(!$tagResult)
							{
								$data['notice'] .= "Tag ".$tag." not added for article";
							}
						}
					}
				}else{
					$data['notice'] = "Article successfully submitted without tags.";
				}
				
				redirect("admin/revisions/".$result['articleId']);
			}else{
				unset($data);
				$data['notice'] = "Error in submission process. Please try again later.";
				var_dump($data);
			}
			
		}
	}	
}

function article_edit($revisionId)
{
	load_helper('form');
	if(!form_submitted())
	{
		$rev['revisionId'] = $revisionId;
		$result = model_exec('article','getDetails', $rev);
		if($result)
		{
			$result['pageJs'] = true;
			$result['js'] = 'submitArticle.js';
			$result['errors'] = false;
			$result['name'] = get_session('user_name');	
			load_view('editArticle',$result, false, true);
		}else{
			echo "Not Found";
		}
	}else{
		$data['errors'] = false;
		
		$data['title'] = $_POST['title'];
		if($data['title'] == "")
		{
			$formError['title'] = "Title cannot be empty.";
			$data['errors'] = true;
		}
		
		$data['category'] = $_POST['category'];
		if($data['category'] == "")
		{
			$formError['category'] = "Category cannot be empty.";
			$data['errors'] = true;
		}
		
		$data['content'] = $_POST['elm1'];
		if($data['content'] == "")
		{
			$formError['elm1'] = "Content cannot be empty.";
			$data['errors'] = true;
		}
		
		$data['tags'] = trim($_POST['tags']);
		$data['categoryId'] = $_POST['categoryId'];
		$data['articleId'] = $_POST['articleId'];
		
		// If errors refresh page.
		if($data['errors'])
		{
			$data['formError'] = $formError;
			$data['name'] = get_session('user_name');
			load_view('submitArticle',$data, false, true);
		}else{
			$article['title'] = $data['title'];
			$article['userId'] = user_id();
			$article['category'] = $data['categoryId'];
			$article['content'] = $data['content'];
			$article['articleId'] = $data['articleId'];
			$tagsList = $data['tags'];
			$result = model_exec('article','submit', $article);
			if($result['revisionId'] != 0)
			{
				unset($data);
				$data['notice'] = "Article successfully submitted.";
				$tagDeleteResult = db_query("DELETE FROM `article_tags` WHERE `article_id`='$result'");
				if($tagsList != "")
				{
					$tags = split(",",$tagsList);
					foreach($tags as $tag)
					{
						$tName = trim($tag);
						if($tName != "")
						{
							$tagName['name'] = $tName;
							$tagName['articleId'] = $result['revisionId'];
							$tagResult = model_exec('article','addTag', $tagName);
							if(!$tagResult)
							{
								$data['notice'] .= "Tag ".$tag." not added for article";
							}
						}
					}
				}else{
					$data['notice'] = "Article successfully submitted without tags.";
				}
				
				redirect("admin/revisions/".$result['articleId']);
			}else{
				unset($data);
				$data['notice'] = "Error in submission process. Please try again later.";
				var_dump($data);
			}
			
		}		
	}
}

function article_getCategories()
{
	$categories = model_exec('article', 'getCategories');
	
	if($categories)
	{
		$data['cats'] = $categories;
		load_view('articleCategories', $data, true, true);
	}
}
?>
