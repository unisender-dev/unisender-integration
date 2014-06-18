<script src="<?php echo WP_PLUGIN_URL.'/'.$this->PLUGIN_FOLDER_NAME.'/js/ru/unisender/formbuilder.js';?>"></script>
<script>
<?php
$this->_translateJs();
?>
</script>
<div class="wrap">
    <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
    <input type="hidden" id="id-formaction" value="<?php echo $formAction;?>"/>
    <h2 class="us-title"><?php _e("Subscribe form builder","unisender");?></h2>
    <br/>
    <table>
        <tr valign="top">
            <th scope="row"><?php _e("Mailing list","unisender");?>:</th>
            <td><?php echo get_option('unisender_list_title');?></td>
        </tr>
    </table>
    <h3><?php _e("Form fields","unisender");?></h3>
    <small><?php _e("You can drag and drop every field to achieve needed order","unisender");?></small>
    <fieldset id="id-fieldsinfocont" style="display: none;">
        <?php
        for($i=0,$len=count($fields);$i<$len;$i++)
        {
        ?>
        <input type="hidden" name="fields[<?php echo $i;?>][name]" value="<?php echo $fields[$i]['name'];?>" rel="<?php echo $fields[$i]['name'];?>"/>
        <input type="hidden" name="fields[<?php echo $i;?>][title]" value="<?php echo $fields[$i]['title'];?>" rel="<?php echo $fields[$i]['name'];?>"/>
        <input type="hidden" name="fields[<?php echo $i;?>][mand]" value="1" rel="<?php echo $fields[$i]['name'];?>" class="mand"/>
        <?php
        }
        ?>
    </fieldset>
    
    <ul class="fieldsortable" id="id-forfields">
        <?php
        for($i=0,$len=count($fields);$i<$len;$i++)
        {
            $isMail = $fields[$i]['name']=="email";
        ?>
        <li class="item" rel="<?php echo $fields[$i]['name'];?>">
            <?php
            if(!$isMail){
                
            ?><span class="rmbtm"><button rel="<?php echo $fields[$i]['name'];?>">х</button></span><?php
                
            }
            ?><span class="mandchk"><input type="checkbox" <?php
            if($isMail){
                echo ' disabled="disabled"';
            }
            ?> rel="<?php echo $fields[$i]['name'];?>" <?php if($fields[$i]['mand']){echo 'checked="checked"';}?>/><sup>*</sup></span>
            <span class="titlename"><span class="title"><?php echo $fields[$i]['title'];?></span><br/><span class="name">(<?php echo $fields[$i]['name'];?>)</span></span>
        </li>
        <?php
        }
        ?>
    </ul>
    <p>* - <?php _e("Mark field as mandatory","unisender");?></p>
    <p><button class="button-secondary action" id="id-addnewfield"><?php _e('Add field',"unisender");?></button></p>

    <div class="formhtml">
        <h4><?php _e("Form's html","unisender");?></h4>
        <textarea class="code" id="id-formhtmlcontainer" name="unisender_subscribe_form"></textarea>
    </div>
    <small><?php _e("You can modify form html before saving, but please do not change form fields and action attribute.","unisender");?></small>
    <p class="submit">
        <input type="submit" class="button-primary" name="unisender_form_email" value="<?php _e('Save Changes','unisender') ?>" />
    </p>   
    </form>
</div>

<div id="id-addfielddlg">
    <div class="wr1">
        <dl>
            <dt><?php _e('Field title',"unisender");?></dt>
            <dd><input type="text" id="id-newfieldtitle" class="regular-text"/></dd>
            <dt><?php _e('Field name',"unisender");?></dt>
            <dd><input type="text" id="id-newfieldname" class="regular-text"/></dd>
            <p id="id-dlgnote"></p>
        </dl>
    </div>
</div>