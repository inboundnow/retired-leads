tx.exe pull -a --skip


for %%a in (*.po) do (
   if /i not "%%~na"=="leads" (
        msgfmt -cv -o "%%~na.mo" "%%a"
        del "%%a"
    )
)

PAUSE