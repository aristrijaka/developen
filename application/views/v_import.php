<div style="margin-top: 20px;">
</div>
<form action="<?php echo base_url();?>index.php/import/upload/" enctype="multipart/form-data" method="post">
<input name="file" type="file" />
<input type="submit" value="Import File" />
</form>

<?php echo $this->session->flashdata('msg'); ?> 