<?php

class M_Message extends CI_Model {

    const ERR_DATA_FETCH = 'Error while trying to fetch list of %ss!';
    const SUCC_DATA_FETCH = 'Successfully fetched list of %ss!';
    const EMPTY_DATA_FETCH = 'There are no entries found.';
    const SUCC_ADD = 'Successfully added new %s!';
    const ERR_ADD = 'Failed to add new %s. Please try again later.';
    const SUCC_UPDATE = 'Successfully updated the %s!';
    const ERR_UPDATE = 'Error while trying to update %s. Please try again later.';
    const SUCC_DELETE = 'Successfully deleted %s!';
    const ERR_DELETE = 'Failed to delete %s. Please try again later.';
    const ERR_NO_PK = 'Please specify the ID of the %s you want to delete.';
    const ERR_DELETE_EXCEPTION = 'The deletion of this %s is restricted';
    const ERR_UPDATE_EXCEPTION = 'Editing of this %s is restricted';

    public function add_success($subject = FALSE, $extras = FALSE) {
        if ($extras) {
            return sprintf(self::SUCC_ADD . " ({$extras})", $subject);
        }
        return sprintf(self::SUCC_ADD, $subject);
    }

    public function add_error($subject = FALSE) {
        return sprintf(self::ERR_ADD, $subject);
    }

    public function update_success($subject = FALSE, $extras = FALSE) {
        if ($extras) {
            return sprintf(self::SUCC_UPDATE . " ({$extras})", $subject);
        }
        return sprintf(self::SUCC_UPDATE, $subject);
    }

    public function update_error($subject = FALSE) {
        return sprintf(self::ERR_UPDATE, $subject);
    }

    public function delete_success($subject = FALSE, $extras = FALSE) {
        if ($extras) {
            return sprintf(self::SUCC_DELETE . " ({$extras})", $subject);
        }
        return sprintf(self::SUCC_DELETE, $subject);
    }

    public function delete_error($subject = FALSE) {
        return sprintf(self::ERR_DELETE, $subject);
    }

    public function no_primary_key_error($subject = FALSE) {
        return sprintf(self::ERR_NO_PK, $subject);
    }

    public function delete_exception($subject = FALSE) {
        return sprintf(self::ERR_DELETE_EXCEPTION, $subject);
    }

    public function update_exception($subject = FALSE) {
        return sprintf(self::ERR_UPDATE_EXCEPTION, $subject);
    }

    public function data_fetch_success($subject = FALSE) {
        return sprintf(self::SUCC_DATA_FETCH, $subject);
    }

    public function data_fetch_error($subject = FALSE) {
        return sprintf(self::ERR_DATA_FETCH, $subject);
    }

    public function data_fetch_empty() {
        return sprintf(self::EMPTY_DATA_FETCH);
    }

}
