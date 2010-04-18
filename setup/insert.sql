
-- populate database with default values --
INSERT INTO tb_config ( label, value ) VALUES ( 'latest_twit_id', 0 );
INSERT INTO tb_config ( label, value ) VALUES ( 'update_stamp', current_timestamp );
INSERT INTO tb_config ( label, value ) VALUES ( 'graph_stamp', current_timestamp );
