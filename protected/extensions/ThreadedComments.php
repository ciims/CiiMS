<?php
/**
 * ThreadedComments class
 * Allows CiiMS to display
 * Adapted From "Jon Gales" @link {http://www.jongales.com/blog/2009/01/27/php-class-for-threaded-comments/}
 */
class ThreadedComments
{

    public $parents  = array();
    public $children = array();

    /**
     * @param array $comments
     */
    function __construct($comments)
    {
        foreach ($comments as $comment)
        {
            if ($comment['parent_id'] == 0)
                $this->parents[$comment['id']][] = $comment;
            else
                $this->children[$comment['parent_id']][] = $comment;
        }
		
		foreach ($this->parents as $c)
            $this->threadParent($c);
    }

    /**
     * @param array $comment
     * @param int $depth
     */
    private function ouputComment($comment, $depth)
    {
        for ($depth; $depth > 0; $depth--)
        {
            echo "<--- ";
        }

        echo $comment['comment'];
        echo "<br />";
    }

    /**
     * @param array $comment
     * @param int $depth
     */
    private function threadParent($comment, $depth = 0)
    {
        foreach ($comment as $c)
        {
            $this->ouputComment($c, $depth);

            if (isset($this->children[$c['id']]))
            {
                $this->threadParent($this->children[$c['id']], $depth + 1);
            }
        }
    }
}
