
delimiter //

DROP PROCEDURE IF EXISTS usp_Tree_insert//
CREATE PROCEDURE `usp_Tree_insert`(IN new_parent_id int, IN new_label varchar(255), IN new_login_id int)
BEGIN
    DECLARE new_left_index INT;
    DECLARE new_path BLOB;
    START TRANSACTION;
    IF new_parent_id > 0 THEN
        SELECT `right_index` INTO new_left_index FROM `Node` WHERE `node_id` = new_parent_id;
        SELECT CONCAT(GROUP_CONCAT(`label` ORDER BY `left_index` ASC SEPARATOR '/'), '/', new_label) INTO new_path
          FROM `Node` WHERE `left_index`<=new_left_index AND `right_index` >=new_left_index;
    ELSE -- We're inserting the first node
        SELECT max( `right_index` ) + 1 INTO new_left_index FROM Node;
        IF new_left_index IS NULL THEN
            SET new_left_index = 1;
        END IF;
        SET new_path=new_label;
    END IF;

    IF new_left_index IS NOT NULL AND new_path IS NOT NULL THEN
        INSERT INTO Node (parent_id, label, path, `left_index`, `right_index`, creator_id, created_uts)
          VALUES (new_parent_id, new_label, new_path, new_left_index, new_left_index + 1, new_login_id, UNIX_TIMESTAMP(NOW()));
        IF @@LAST_INSERT_ID > 0 AND new_parent_id > 0 THEN -- UPDATE the traversal indexes
             UPDATE Node SET `left_index`  = `left_index`  + 2 WHERE `node_id` != @@LAST_INSERT_ID AND `left_index`  >= new_left_index;
             UPDATE Node SET `right_index` = `right_index` + 2 WHERE `node_id` != @@LAST_INSERT_ID AND `right_index` >= new_left_index;
             SELECT @@LAST_INSERT_ID;
        END IF;
    END IF;
    COMMIT;
END
//

DROP PROCEDURE IF EXISTS usp_Tree_delete//
CREATE PROCEDURE `usp_Tree_delete`(IN old_node_Id int, IN _login_id int, IN recursive bool)
BEGIN
    DECLARE old_left_index INT;
    DECLARE old_right_index INT;
    START TRANSACTION;
    SELECT `right_index`,   `left_index`,   `creator_id`
      INTO old_right_index, old_left_index, @tmp
      FROM Node WHERE node_id = old_node_Id;
    IF @tmp = _login_id THEN
        IF recursive = true OR old_right_index-old_left_index = 1 THEN
            DELETE FROM Node WHERE `left_index` >= old_left_index AND `right_index` <= old_right_index AND creator_id = _login_id;
            UPDATE Node SET `left_index`  = -1, `right_index` = -1, `parent_id` = -1, path = '' WHERE `left_index` >= old_left_index AND `right_index` <= old_right_index;
            UPDATE Node SET `left_index`  = `left_index`  -(old_right_index-old_left_index) - 1 WHERE `left_index`  > old_right_index;
            UPDATE Node SET `right_index` = `right_index` -(old_right_index-old_left_index) - 1 WHERE `right_index` > old_right_index;
        END IF;
    END IF;
    COMMIT;
END
//

DROP PROCEDURE IF EXISTS usp_Tree_touch//
CREATE PROCEDURE `usp_Tree_touch`(IN _node_Id int, IN _login_id int)
BEGIN
    UPDATE Node SET updated_dts = now() WHERE node_id = _node_Id AND creator_id = _login_id;
    SELECT ROW_COUNT();
END
//

DROP PROCEDURE IF EXISTS usp_Tree_update//
CREATE PROCEDURE `usp_Tree_update`(IN _node_Id int, IN _login_id int, IN new_parent_id int, IN new_label varchar(255))
BEGIN
    DECLARE old_lef, old_rig, old_parent_id INT;
    DECLARE old_label varchar(255);
    DECLARE old_path mediumtext;
    START TRANSACTION;
    SELECT creator_id INTO @tmp FROM Node WHERE node_id = _node_Id;
    IF @tmp = _login_id THEN
        SELECT node_id INTO @tmp FROM Node WHERE node_id = new_parent_id;
        IF @tmp IS NOT NULL THEN
            SELECT right_index, left_index, label,     path,     parent_id
              INTO old_rig,     old_lef,    old_label, old_path, old_parent_id
              FROM Node WHERE node_id = _node_Id;
            IF old_parent_id != new_parent_id OR old_label != new_label THEN -- UPDATE the affected paths
                BEGIN
                 DECLARE p_path, old_path_len mediumtext;
                 SELECT path,   LENGTH(old_path)+1
                   INTO p_path, old_path_len
                   FROM Node WHERE node_id=new_parent_id;
                 UPDATE Node SET path = CONCAT(p_path, '/', new_label, SUBSTRING(path, old_path_len))
                  WHERE left_index >= old_lef AND right_index <= old_rig;
                END;
            END IF;
            IF old_parent_id!=new_parent_id THEN -- UPDATE the affected left/right indexes
                BEGIN
                 DECLARE p_lef, p_rig INT;
                 SELECT right_index, left_index INTO p_rig, p_lef FROM Node WHERE node_id = new_parent_id;
                 SET @gapSize = old_rig-old_lef+1;
                 UPDATE Node SET `left_index`  = `left_index`  +@gapSize WHERE `left_index`  >= p_rig;
                 UPDATE Node SET `right_index` = `right_index` +@gapSize WHERE `right_index` >= p_rig;
                 UPDATE Node SET parent_id = new_parent_id WHERE node_id = _node_Id;
                 IF p_rig<old_rig THEN
                  SET @newLeft=old_lef+@gapSize;
                 ELSE
                  SET @newLeft=old_lef;
                 END IF;
                 UPDATE Node SET `left_index`  = `left_index`+(p_rig-@newLeft), `right_index` = `right_index`+(p_rig-@newLeft)
                  WHERE `left_index` >= @newLeft AND `left_index` < @newLeft + @gapSize-1;
                 UPDATE Node SET `left_index`  = `left_index`  -@gapSize WHERE `left_index`  > old_rig;
                 UPDATE Node SET `right_index` = `right_index` -@gapSize WHERE `right_index` > old_rig;
                END;
            END IF;
            UPDATE Node SET label = new_label WHERE node_id = _node_Id;
        END IF;
    END IF;
    COMMIT;
    SELECT ROW_COUNT();
END//

delimiter ;

CALL `usp_Tree_insert`(0, '', 1);
