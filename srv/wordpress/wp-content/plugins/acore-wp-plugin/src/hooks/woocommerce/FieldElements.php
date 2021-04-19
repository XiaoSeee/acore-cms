<?php

namespace ACore;

use ACore\ACoreServices;

class FieldElements {

    public static function charList($username, $deleted = false) {
        $ACoreSrv = ACoreServices::I();
        $accRepo = $ACoreSrv->getAccountRepo();
        $charRepo = $ACoreSrv->getCharactersRepo();

        $account = $accRepo->findOneByUsername($username);
        if (!$account) {
          // if the account does not exist in the core database
          echo '<br><br><span style="color: red;">请先登录一个有效的游戏账户！</span><br>';
          return;
        }

        $accountId = $account->getId();
        $characters = $charRepo->findByAccount($accountId);
        $deletedCharacters = $charRepo->findByDeleteInfos_Account($accountId);
        $charBanRepo = $ACoreSrv->getCharactersBannedRepo();
        $accBanRepo = $ACoreSrv->getAccountBannedRepo();

        if ($accBanRepo->isActiveById($accountId)) {
            echo '<br><br><span style="color: red;">您的账户已经被禁用！</span><br>';
            return;
        }

        if ($deleted && count($deletedCharacters) == 0) {
            echo '<br><span style="color: red;">您并没有已经删除的角色用于恢复。</span><br><br><br>';
            return;
        }

        $bannedChars = array();
        ?>
        <label for="acore_char_sel">选择您接收礼品角色： </label>
        <select id="acore_char_sel" class="acore_char_sel" name="acore_char_sel">
            <?php

            if (!$deleted) {
                foreach ($characters as $key => $value):
                    if ($charBanRepo->isActiveByGuid($value->getGuid())) {
                        $bannedChars[] = $value->getName();
                        continue;
                    }
                    echo '<option value="' . $value->getGuid() . '">' . $value->getName() . '</option>';
                endforeach;
            } else {
                foreach ($deletedCharacters as $key => $value):
                    echo '<option value="' . $value->getGuid() . '">' . $value->getDeletedName() . '</option>';
                endforeach;
            }

            ?>
        </select>
        <br>
        <?php
        if ($bannedChars) {
            echo "您这些角色已经被禁用： <br>";
            echo implode(",", $bannedChars) . "<br>";
        }
    }

    public static function destCharacter($label) {
        ?>
        <label for="acore_char_dest"><?= $label ?></label>
        <input type="text" placeholder="角色名" id="acore_char_dest" class="acore_char_dest" name="acore_char_dest">
        <br>
        <?php
    }

    public static function destAccount() {
        ?>
        <label for="acore_dest_account">目标账户： </label>
        <input required type="text" id="acore_dest_account" class="acore_dest_account" name="acore_dest_account">
        <br>
        <?php
    }

}
