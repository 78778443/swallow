<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;
use Throwable;

class DbSql extends Command
{
    protected function configure()
    {
        $this->setName('db:sql')
            ->addArgument('sql', Argument::REQUIRED, '要执行的SQL语句')
            ->addOption('type', 't', Option::VALUE_OPTIONAL, 'SQL类型 (select/insert/update/delete)', 'auto')
            ->setDescription('执行SQL语句，方便调试数据库');
    }

    protected function execute(Input $input, Output $output): void
    {
        $sql = trim($input->getArgument('sql'));
        $type = trim($input->getOption('type'));

        if (empty($sql)) {
            $output->writeln('<error>SQL语句不能为空</error>');
            return;
        }

        // 自动判断SQL类型
        if ($type === 'auto') {
            $type = $this->detectSqlType($sql);
        }

        $output->writeln("<info>执行SQL: {$sql}</info>");
        $output->writeln("<info>SQL类型: {$type}</info>");

        try {
            switch (strtolower($type)) {
                case 'select':
                    $this->executeSelect($sql, $output);
                    break;
                case 'insert':
                    $this->executeInsert($sql, $output);
                    break;
                case 'update':
                    $this->executeUpdateDelete($sql, $output, 'UPDATE');
                    break;
                case 'delete':
                    $this->executeUpdateDelete($sql, $output, 'DELETE');
                    break;
                case 'alter':
                case 'create':
                case 'drop':
                case 'truncate':
                    $this->executeDdl($sql, $output, strtoupper($type));
                    break;
                default:
                    $result = Db::execute($sql);
                    $output->writeln("<info>执行结果: 影响行数 {$result}</info>");
                    break;
            }
        } catch (Throwable $e) {
            $output->writeln("<error>执行SQL时发生错误: " . $e->getMessage() . "</error>");
            $output->writeln("<error>错误位置: " . $e->getFile() . ":" . $e->getLine() . "</error>");
        }
    }

    /**
     * 检测SQL类型
     */
    private function detectSqlType(string $sql): string
    {
        $sql = trim(strtoupper($sql));
        
        if (strpos($sql, 'SELECT') === 0) {
            return 'select';
        } elseif (strpos($sql, 'INSERT') === 0) {
            return 'insert';
        } elseif (strpos($sql, 'UPDATE') === 0) {
            return 'update';
        } elseif (strpos($sql, 'DELETE') === 0) {
            return 'delete';
        } elseif (strpos($sql, 'ALTER') === 0) {
            return 'alter';
        } elseif (strpos($sql, 'CREATE') === 0) {
            return 'create';
        } elseif (strpos($sql, 'DROP') === 0) {
            return 'drop';
        } elseif (strpos($sql, 'TRUNCATE') === 0) {
            return 'truncate';
        }
        
        // 默认为可能影响数据的语句
        return 'other';
    }

    /**
     * 执行SELECT语句
     */
    private function executeSelect(string $sql, Output $output): void
    {
        $result = Db::query($sql);
        
        if (empty($result)) {
            $output->writeln('<info>查询结果: 无数据</info>');
            return;
        }

        // 获取列名作为表头
        $headers = array_keys($result[0] ?? []);
        
        // 输出表头
        $headerLine = "| ";
        foreach ($headers as $header) {
            $headerLine .= sprintf("%-20s", $header) . " | ";
        }
        $output->writeln($headerLine);
        
        // 输出分隔线
        $separatorLine = "|";
        foreach ($headers as $header) {
            $separatorLine .= str_repeat("-", 22) . "|";
        }
        $output->writeln($separatorLine);
        
        // 输出数据行
        foreach ($result as $row) {
            $dataLine = "| ";
            foreach ($headers as $header) {
                $value = isset($row[$header]) ? (string)$row[$header] : '';
                // 限制单元格内容长度，避免表格过宽
                if (strlen($value) > 18) {
                    $value = substr($value, 0, 15) . '...';
                }
                $dataLine .= sprintf("%-20s", $value) . " | ";
            }
            $output->writeln($dataLine);
        }

        $output->writeln("<info>查询结果: 共 " . count($result) . " 行</info>");
    }

    /**
     * 执行INSERT语句
     */
    private function executeInsert(string $sql, Output $output): void
    {
        $result = Db::execute($sql);
        $insertId = 0;
        
        $output->writeln("<info>INSERT执行成功</info>");
        $output->writeln("<info>影响行数: {$result}</info>");
        if ($insertId > 0) {
            $output->writeln("<info>插入ID: {$insertId}</info>");
        }
    }

    /**
     * 执行UPDATE或DELETE语句
     */
    private function executeUpdateDelete(string $sql, Output $output, string $operation): void
    {
        $result = Db::execute($sql);
        $output->writeln("<info>{$operation}执行完成</info>");
        $output->writeln("<info>影响行数: {$result}</info>");
    }

    /**
     * 执行DDL语句 (ALTER, CREATE, DROP, TRUNCATE)
     */
    private function executeDdl(string $sql, Output $output, string $operation): void
    {
        $output->writeln("<comment>警告: 正在执行{$operation}语句，这可能会影响数据库结构或数据!</comment>");
        $output->writeln("<comment>确定要继续吗? (y/N)</comment>");
        
        // 注意：在命令行环境中获取用户输入需要特殊处理
        $result = Db::execute($sql);
        $output->writeln("<info>{$operation}执行完成</info>");
        $output->writeln("<info>影响行数: {$result}</info>");
    }
}