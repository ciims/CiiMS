<?php
/**
 * ThreadedComments class
 * Allows CiiMS to display
 * Adapted From "Jon Gales" @link {http://www.jongales.com/blog/2009/01/27/php-class-for-threaded-comments/}
 */
class ThreadedComments
{

    public  $parents  = array();

    public  $dashboard= false;
    public  $children = array();

    private $md       = array();

    /**
     * @param array $comments
     */
    function __construct($comments, $dashboard = false)
    {
        $this->dashboard = $dashboard;

        foreach ($comments as $comment)
        {
            if ($comment['parent_id'] == 0)
                $this->parents[$comment['id']][] = $comment;
            else
                $this->children[$comment['parent_id']][] = $comment;
        }

        $this->md = new CMarkdownParser();

		foreach ($this->parents as $c)
            $this->threadParent($c);
    }

    /**
     * Outputs the comment to display at a given depth
     * @param array $comment
     * @param int $depth
     */
    private function ouputComment($comment = NULL, $depth = 0)
    {
        if ($this->dashboard)
        {
            $path = 'application.modules.dashboard.views.content.comments';
            $depth = 0;
        }
        else
            $path = 'webroot.themes.'.Yii::app()->theme->name.'.views.comment.comment';

        echo Yii::app()->controller->renderPartial($path, array('comment' => $comment, 'depth' => $depth, 'md' => $this->md));
    }

    /**
     * @param array $comment
     * @param int $depth
     */
    private function threadParent($comment, $depth = 0)
    {
        foreach ($comment as $c)
        {
            if ($c['approved'] != 1)
                continue;

            $this->ouputComment($c, $depth);

            if (isset($this->children[$c['id']]))
                $this->threadParent($this->children[$c['id']], min($depth + 1, 3));
        }
    }
}
