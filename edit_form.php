<?php

class block_sic_edit_form extends block_edit_form{

    protected function specific_definition($mform){
        global $DB, $COURSE;

        $mform->addElement('header', 'config_header', get_string('tituloheader', 'block_sic').' Curso: '.$COURSE->id);

        $mform->addElement('hidden', 'config_sic_courseid', $COURSE->id);

        $mform->addElement('selectyesno', 'config_sic_status', get_string('txtstatustask', 'block_sic'));
        $mform->setDefault('config_sic_status', 0);
        // $mform->setType('config_sic_status', PARAM_INT);

        $mform->addElement('text', 'config_sic_codigo_grupo', get_string('txtcodigogrupo', 'block_sic'));
        $mform->setType('config_sic_codigo_grupo', PARAM_RAW);

        $mform->addElement('text', 'config_sic_codigo_oferta', get_string('txtcodigooferta', 'block_sic'));
        $mform->setType('config_sic_codigo_oferta', PARAM_RAW);

        //Frecuencia
        // $freq = array(
        //     0 => "1 vez al dia",
        //     1 => "2 veces al dia",
        //     2 => "2 veces a la semana",
        //     3 => "1 vez a la semana",
        //     4 => "2 veces al mes",
        //     5 => "1 vez al mes"
        // );
        // $mform->addElement('select', 'config_sic_frecuencia', get_string('txtfrecuencia', 'block_sic'), $freq);
        // $mform->setDefault('confg_sic_frecuencia', 0);
        // $mform->setType('config_sic_frecuencia', PARAM_INT);

        // //Hora del dia
        // $hour = array(
        //     0 =>'00:00', 12 =>'12:00',
        //     1 =>'01:00', 13 =>'13:00',
        //     2 =>'02:00', 14 =>'14:00',
        //     3 =>'03:00', 15 =>'15:00',
        //     4 =>'04:00', 16 =>'16:00',
        //     5 =>'05:00', 17 =>'17:00',
        //     6 =>'06:00', 18 =>'18:00',
        //     7 =>'07:00', 19 =>'19:00',
        //     8 =>'08:00', 20 =>'20:00',
        //     9 =>'09:00', 21 =>'21:00',
        //     10 =>'10:00', 22 =>'22:00',
        //     11 =>'11:00', 23 =>'23:00',
            
        // );
        // $mform->addElement('select', 'config_sic_hora', get_string('txthora', 'block_sic'), $hour);
        // $mform->setDefault('config_sic_hora', 0);
        // $mform->setType('config_sic_hora', PARAM_INT);

        //Roles
        $arr_roles = array(-1=>"Todos");
        $roles = role_fix_names(get_all_roles(), null, ROLENAME_ORIGINAL);
        foreach($roles as $role){
            $arr_roles[$role->id] = $role->id.' - '.$role->localname;
        }
        $mform->addElement('select', 'config_sic_rol', get_string('txtrol', 'block_sic'), $arr_roles);
        $mform->setDefault('config_sic_rol', 5);
        $mform->setType('config_sic_rol', PARAM_INT);
        
    }
}

?>