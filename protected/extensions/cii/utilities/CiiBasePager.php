<?php

class CiiBasePager extends CBasePager 
{
	
	/**
     * Creates the default pagination.
     * This is called by {@link getPages} when the pagination is not set before.
     * @return CPagination the default pagination instance.
     */
    protected function createPages()
    {
            return new CiiPagination;
    }
}

?>