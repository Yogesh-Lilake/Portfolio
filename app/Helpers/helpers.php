<?php

function safe_fetch(PDOStatement|false $stmt)
{
    if (!$stmt) return null;
    try {
        return $stmt->fetch();
    } catch (Throwable $e) {
        app_log("Fetch Error", "error", ["error" => $e->getMessage()]);
        return null;
    }
}

function safe_fetch_all(PDOStatement|false $stmt)
{
    if (!$stmt) return [];
    try {
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        app_log("FetchAll Error", "error", ["error" => $e->getMessage()]);
        return [];
    }
}

function safe_query($sql)
{
    $pdo = DB::getInstance()->pdo();
    if (!$pdo) return false;

    try {
        return $pdo->query($sql);
    } catch (Throwable $e) {
        app_log("SQL Error", "error", ["sql" => $sql, "error" => $e->getMessage()]);
        return false;
    }
}
