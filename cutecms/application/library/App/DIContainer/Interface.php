<?php

interface App_DIContainer_Interface
{

	/**
	 * @param string name of interface to be replaced
	 * @param string name of class that interface is replaced by
	 * @return App_DIContainer_Interface $this
	 */
	public function inject($interface, $class);

	/**
	 * @param requested interface of
	 * @param1,...@paramN - parameters to constructor
	 */
	public function getObject();

}