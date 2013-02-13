<h4>All Done!</h4>
<p>CiiMS has finished the installation, you are now ready to login. Click the login button below to login to your site.</p>

<p>As a reminder, now would be a good time to secure your installation. Details on how to secure your installation can be found <?php echo CHtml::link('on github', 'https://github.com/charlesportwoodii/CiiMS/blob/master/SECURE.md', array('target' => '_blank')); ?>. I hope you enjoy CiiMS!</p>
<center>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => 'Login Now',
        'type' => 'success',
        'size' => 'large',
        'url' => $this->createUrl('/admin')
    )); ?>
</center>