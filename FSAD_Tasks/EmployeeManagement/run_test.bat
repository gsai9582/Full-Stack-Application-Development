@echo off
setlocal enabledelayedexpansion
set /p CP=<target\cp.txt
java -cp "target\classes;%CP%" com.project.main.MainApp < test_inputs.txt

