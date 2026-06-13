# Safe ASCII script with dynamic UTF8 base64-decoded messages
Add-Type -AssemblyName PresentationFramework, System.Xaml, WindowsBase

function Do-Events {
    $frame = New-Object System.Windows.Threading.DispatcherFrame
    [System.Windows.Threading.Dispatcher]::CurrentDispatcher.BeginInvoke(
        [System.Windows.Threading.DispatcherPriority]::Background,
        [Action[System.Windows.Threading.DispatcherFrame]]{ param($f) $f.Continue = $false },
        $frame
    )
    [System.Windows.Threading.Dispatcher]::PushFrame($frame)
}

function Get-Thai {
    param([string]$base64)
    return [System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String($base64))
}

# Message table
$msg = @{
    step1_run = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LiV4Lij4Lin4LiI4Liq4Lit4LiaIFBIUCDguYPguJnguKPguLDguJrguJouLi4="
    step1_found = Get-Thai "4Lie4LiaIFBIUDog"
    step1_fail = Get-Thai "4LmE4Lih4LmI4Lie4LiaIFBIUCEg4LiB4Lij4Li44LiT4Liy4LiV4Li04LiU4LiV4Lix4LmJ4LiHIExhcmFnb24g4Lir4Lij4Li34LitIFhBTVBQ"
    status_err_no_php = Get-Thai "4LmA4LiB4Li04LiU4LiC4LmJ4Lit4Lic4Li04LiU4Lie4Lil4Liy4LiUOiDguYTguKHguYjguJ7guJogUEhQIOC5g+C4meC4o+C4sOC4muC4mg=="
    btn_close = Get-Thai "4Lib4Li04LiU4Lir4LiZ4LmJ4Liy4LiV4LmI4Liy4LiH"
    step2_check = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LiV4Lij4Lin4LiI4Liq4Lit4Lia4Liq4LiW4Liy4LiZ4LiwIE15U1FMIERhdGFiYXNlLi4u"
    step2_running = Get-Thai "4LiQ4Liy4LiZ4LiC4LmJ4Lit4Lih4Li54LilIE15U1FMIOC4geC4s+C4peC4seC4h+C4l+C4s+C4h+C4suC4meC4reC4ouC4ueC5iOC4geC5iOC4reC4meC5geC4peC5ieC4pw=="
    step2_starting = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LmA4Lib4Li04LiU4Lij4Liw4Lia4Lia4LiQ4Liy4LiZ4LiC4LmJ4Lit4Lih4Li54LilIE15U1FMLi4u"
    step2_success = Get-Thai "4LmA4Lib4Li04LiU4Lij4Liw4Lia4Lia4LiQ4Liy4LiZ4LiC4LmJ4Lit4Lih4Li54LilIE15U1FMIOC4quC4s+C5gOC4o+C5h+C4iA=="
    step2_fail = Get-Thai "4Lil4Lit4LiH4LmA4Lib4Li04LiUIE15U1FMIOC5geC4peC5ieC4pyDguYHguJXguYjguJXguKPguKfguIjguKrguK3guJrguKrguJbguLLguJnguLDguYTguKHguYjguKrguLPguYDguKPguYfguIg="
    step2_no_dir = Get-Thai "4LmE4Lih4LmI4Lie4Lia4LmE4LiU4LmA4Lij4LiB4LiX4Lit4Lij4Li1IE15U1FMISAo4Lir4Liy4LiB4LmA4Lib4Li04LiUIExhcmFnb24vWEFNUFAg4LmA4Lit4LiH4LmB4Lil4LmJ4LinIOC4quC4suC4oeC4suC4o+C4luC4geC4lOC4guC5ieC4suC4oeC5hOC4lOC5iSk="
    step3_running = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LiV4Li04LiU4LiV4Lix4LmJ4LiH4LmA4LiL4Li04Lij4LmM4Lif4LmA4Lin4Lit4Lij4LmM4LiI4Liz4Lil4Lit4LiHIFBIUCAobG9jYWxob3N0OjgwMDApLi4u"
    step3_success = Get-Thai "UEhQIFdlYiBTZXJ2ZXIg4LmA4Lij4Li04LmI4Lih4LiX4Liz4LiH4Liy4LiZ4LmB4Lil4LmJ4Lin"
    step3_fail = Get-Thai "4LmA4LiB4Li04LiU4LiC4LmJ4Lit4Lic4Li04LiU4Lie4Lil4Liy4LiU4LmD4LiZ4LiB4Liy4Lij4LmA4Lib4Li04LiU4LmA4LiL4Li04Lij4LmM4Lif4LmA4Lin4Lit4Lij4LmMIQ=="
    status_server_fail = Get-Thai "4LmA4LiL4Li04Lij4LmM4Lif4LmA4Lin4Lit4Lij4LmM4LiX4Liz4LiH4Liy4LiZ4Lil4LmJ4Lih4LmA4Lir4Lil4Lin"
    step4_running = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LmA4Lib4Li04LiU4LmA4Lin4LmH4Lia4LmE4LiL4LiV4LmM4LmD4LiZ4Lir4LiZ4LmJ4Liy4LmA4Lin4LmH4Lia4LmA4Lia4Lij4Liy4Lin4LmM4LmA4LiL4Lit4Lij4LmM4LiC4Lit4LiH4LiE4Li44LiTLi4u"
    step4_success = Get-Thai "4LmA4Lib4Li04LiU4Lir4LiZ4LmJ4Liy4LmA4Lia4Lij4Liy4Lin4LmM4LmA4LiL4Lit4Lij4LmM4LmA4Lij4Li14Lii4Lia4Lij4LmJ4Lit4Lii4LmB4Lil4LmJ4LinIQ=="
    status_done = Get-Thai "4LiX4Li44LiB4LiB4Lij4Liw4Lia4Lin4LiZ4LiB4Liy4Lij4LmA4Liq4Lij4LmH4LiI4Liq4Lih4Lia4Li54Lij4LiT4LmMISDguKvguJnguYnguLLguYDguKfguYfguJrguYDguJvguLTguJTguYHguKXguYnguKc="
    btn_stop_server = Get-Thai "4Lir4Lii4Li44LiU4LmA4LiL4Li04Lij4LmM4Lif4LmA4Lin4Lit4Lij4LmMICYg4Lib4Li04LiU4Lij4Liw4Lia4Lia"
}

$xaml = @'
<Window xmlns="http://schemas.microsoft.com/winfx/2006/xaml/presentation"
        xmlns:x="http://schemas.microsoft.com/winfx/2006/xaml"
        Title="Luang Prabang Heritage Local Server Control"
        Height="380" Width="480"
        WindowStartupLocation="CenterScreen"
        WindowStyle="None"
        AllowsTransparency="True"
        Background="Transparent"
        Topmost="True">
    <Border CornerRadius="16" Background="#0f172a" BorderBrush="#3b82f6" BorderThickness="2">
        <Grid Margin="24">
            <Grid.RowDefinitions>
                <RowDefinition Height="Auto"/> <!-- Header -->
                <RowDefinition Height="*"/>    <!-- Steps -->
                <RowDefinition Height="Auto"/> <!-- Status & Progress -->
                <RowDefinition Height="Auto"/> <!-- Action Button -->
            </Grid.RowDefinitions>
            
            <!-- Header -->
            <StackPanel Grid.Row="0" Margin="0,0,0,16">
                <Grid>
                    <TextBlock Text="LUANG PRABANG HERITAGE" Foreground="#60a5fa" FontSize="18" FontWeight="Bold" HorizontalAlignment="Center"/>
                    <!-- Close button in top right -->
                    <Button x:Name="BtnCloseTop" Content="&#x2715;" Width="24" Height="24" Background="Transparent" Foreground="#64748b" BorderThickness="0" FontWeight="Bold" FontSize="14" HorizontalAlignment="Right" VerticalAlignment="Top" Cursor="Hand"/>
                </Grid>
                <TextBlock Text="&#x0E23;&#x0E30;&#x0E1A;&#x0E1A;&#x0E40;&#x0E23;&#x0E34;&#x0E48;&#x0E21;&#x0E40;&#x0E0B;&#x0E34;&#x0E23;&#x0E4C;&#x0E1F;&#x0E40;&#x0E27;&#x0E2D;&#x0E23;&#x0E4C;&#x0E08;&#x0E33;&#x0E25;&#x0E2D;&#x0E07;&#x0E2A;&#x0E33;&#x0E2B;&#x0E23;&#x0E31;&#x0E1A;&#x0E19;&#x0E31;&#x0E01;&#x0E1E;&#x0E31;&#x0E12;&#x0E19;&#x0E32; (Local Server Control)" Foreground="#64748b" FontSize="11" HorizontalAlignment="Center" Margin="0,4,0,0"/>
            </StackPanel>
            
            <!-- Steps Panel -->
            <StackPanel Grid.Row="1" VerticalAlignment="Center" Margin="10,0,10,0">
                <!-- Step 1 -->
                <Grid Margin="0,8,0,8">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep1" Text="&#x25CB;" Foreground="#64748b" FontSize="14" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep1" Grid.Column="1" Text="&#x0E15;&#x0E23;&#x0E27;&#x0E08;&#x0E2A;&#x0E2D;&#x0E1A;&#x0E04;&#x0E27;&#x0E32;&#x0E21;&#x0E1E;&#x0E23;&#x0E49;&#x0E2D;&#x0E21;&#x0E02;&#x0E2D;&#x0E07; PHP..." Foreground="#94a3b8" FontSize="13" VerticalAlignment="Center"/>
                </Grid>
                <!-- Step 2 -->
                <Grid Margin="0,8,0,8">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep2" Text="&#x25CB;" Foreground="#64748b" FontSize="14" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep2" Grid.Column="1" Text="&#x0E01;&#x0E33;&#x0E25;&#x0E31;&#x0E07;&#x0E40;&#x0E23;&#x0E34;&#x0E48;&#x0E21;&#x0E23;&#x0E30;&#x0E1A;&#x0E1A;&#x0E10;&#x0E32;&#x0E19;&#x0E02;&#x0E49;&#x0E2D;&#x0E21;&#x0E39;&#x0E25; MySQL (Laragon/XAMPP)..." Foreground="#94a3b8" FontSize="13" VerticalAlignment="Center"/>
                </Grid>
                <!-- Step 3 -->
                <Grid Margin="0,8,0,8">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep3" Text="&#x25CB;" Foreground="#64748b" FontSize="14" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep3" Grid.Column="1" Text="&#x0E40;&#x0E23;&#x0E34;&#x0E48;&#x0E21;&#x0E43;&#x0E0A;&#x0E49;&#x0E07;&#x0E32;&#x0E19;&#x0E40;&#x0E0B;&#x0E34;&#x0E23;&#x0E4C;&#x0E1F;&#x0E40;&#x0E27;&#x0E2D;&#x0E23;&#x0E4C;&#x0E08;&#x0E33;&#x0E25;&#x0E2D;&#x0E07; PHP Web Server..." Foreground="#94a3b8" FontSize="13" VerticalAlignment="Center"/>
                </Grid>
                <!-- Step 4 -->
                <Grid Margin="0,8,0,8">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep4" Text="&#x25CB;" Foreground="#64748b" FontSize="14" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep4" Grid.Column="1" Text="&#x0E40;&#x0E1B;&#x0E34;&#x0E14;&#x0E2B;&#x0E19;&#x0E49;&#x0E32;&#x0E40;&#x0E27;&#x0E47;&#x0E1A;&#x0E44;&#x0E0B;&#x0E15;&#x0E4C;&#x0E43;&#x0E19;&#x0E40;&#x0E27;&#x0E47;&#x0E1A;&#x0E40;&#x0E1A;&#x0E23;&#x0E32;&#x0E27;&#x0E4C;&#x0E40;&#x0E0B;&#x0E2D;&#x0E23;&#x0E4C; (localhost:8000)..." Foreground="#94a3b8" FontSize="13" VerticalAlignment="Center"/>
                </Grid>
            </StackPanel>
            
            <!-- Status & Progress -->
            <StackPanel Grid.Row="2" Margin="0,12,0,16">
                <ProgressBar x:Name="ProgressBar" Height="6" Minimum="0" Maximum="100" Value="0" Background="#1e293b" Foreground="#3b82f6" BorderThickness="0" Margin="0,0,0,8"/>
                <TextBlock x:Name="StatusText" Text="&#x0E01;&#x0E33;&#x0E25;&#x0E31;&#x0E07;&#x0E40;&#x0E15;&#x0E23;&#x0E35;&#x0E22;&#x0E21;&#x0E23;&#x0E30;&#x0E1A;&#x0E1A;..." Foreground="#60a5fa" FontSize="12" FontWeight="SemiBold" HorizontalAlignment="Center"/>
            </StackPanel>
            
            <!-- Action Button -->
            <Grid Grid.Row="3">
                <Button x:Name="BtnAction" Content="&#x0E01;&#x0E33;&#x0E25;&#x0E31;&#x0E07;&#x0E15;&#x0E23;&#x0E27;&#x0E08;&#x0E2A;&#x0E2D;&#x0E1A;..." Height="36" Width="180" Background="#1e293b" Foreground="#64748b" BorderThickness="0" FontWeight="Bold" FontSize="13" HorizontalAlignment="Center" IsEnabled="False" Cursor="Hand">
                    <Button.Template>
                        <ControlTemplate TargetType="Button">
                            <Border Background="{TemplateBinding Background}" CornerRadius="8">
                                <ContentPresenter HorizontalAlignment="Center" VerticalAlignment="Center"/>
                            </Border>
                        </ControlTemplate>
                    </Button.Template>
                </Button>
            </Grid>
        </Grid>
    </Border>
</Window>
'@

$window = [Windows.Markup.XamlReader]::Parse($xaml)

# Element References
$iconStep1 = $window.FindName("IconStep1")
$textStep1 = $window.FindName("TextStep1")
$iconStep2 = $window.FindName("IconStep2")
$textStep2 = $window.FindName("TextStep2")
$iconStep3 = $window.FindName("IconStep3")
$textStep3 = $window.FindName("TextStep3")
$iconStep4 = $window.FindName("IconStep4")
$textStep4 = $window.FindName("TextStep4")
$progressBar = $window.FindName("ProgressBar")
$statusText = $window.FindName("StatusText")
$btnAction = $window.FindName("BtnAction")
$btnCloseTop = $window.FindName("BtnCloseTop")

# Support window drag
$window.Add_MouseLeftButtonDown({
    $this.DragMove()
})

# Close top button event
$btnCloseTop.Add_Click({
    $window.Close()
})

$global:phpProcess = $null

# Closing event helper to ensure PHP gets killed
$window.Add_Closing({
    if ($global:phpProcess) {
        Stop-Process -Id $global:phpProcess.Id -Force -ErrorAction SilentlyContinue
    }
})

$btnAction.Add_Click({
    $window.Close()
})

# UI Updater helper
function Set-Status {
    param(
        [int]$ProgressVal,
        [string]$Message
    )
    $progressBar.Value = $ProgressVal
    $statusText.Text = $Message
    Do-Events
}

# Run tasks asynchronously once window is loaded
$window.Add_Loaded({
    # --- Step 1: Verify PHP ---
    $iconStep1.Text = [char]0x23F3
    $iconStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250)) # Blue
    $textStep1.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 10 $msg.step1_run
    Start-Sleep -Milliseconds 600

    $phpPath = "php"
    $checkPhp = Get-Command php -ErrorAction SilentlyContinue
    if (-not $checkPhp) {
        $phpPath = $null
        # Try Laragon D:
        if (Test-Path "D:\laragon\bin\php") {
            $dirs = Get-ChildItem "D:\laragon\bin\php\php-*" | Sort-Object Name -Descending
            foreach ($d in $dirs) {
                if (Test-Path "$($d.FullName)\php.exe") {
                    $phpPath = "$($d.FullName)\php.exe"
                    break
                }
            }
        }
        # Try Laragon C:
        if (-not $phpPath -and (Test-Path "C:\laragon\bin\php")) {
            $dirs = Get-ChildItem "C:\laragon\bin\php\php-*" | Sort-Object Name -Descending
            foreach ($d in $dirs) {
                if (Test-Path "$($d.FullName)\php.exe") {
                    $phpPath = "$($d.FullName)\php.exe"
                    break
                }
            }
        }
        # Try XAMPP C:
        if (-not $phpPath -and (Test-Path "C:\xampp\php\php.exe")) {
            $phpPath = "C:\xampp\php\php.exe"
        }
    }

    if ($phpPath) {
        $iconStep1.Text = [char]0x2705
        $iconStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129)) # Green
        $textStep1.Text = $msg.step1_found + (Split-Path $phpPath -Leaf)
        $textStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184)) # Muted
    } else {
        $iconStep1.Text = [char]0x274C
        $iconStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68)) # Red
        $textStep1.Text = $msg.step1_fail
        Set-Status 0 $msg.status_err_no_php
        $btnAction.Content = $msg.btn_close
        $btnAction.Foreground = [System.Windows.Media.Brushes]::White
        $btnAction.Background = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $btnAction.IsEnabled = $true
        return
    }

    # --- Step 2: Verify & Start MySQL ---
    $iconStep2.Text = [char]0x23F3
    $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep2.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 35 $msg.step2_check
    Start-Sleep -Milliseconds 600

    $mysqlPath = "mysqld"
    $checkMysql = Get-Command mysqld -ErrorAction SilentlyContinue
    if (-not $checkMysql) {
        $mysqlPath = $null
        # Try Laragon D:
        if (Test-Path "D:\laragon\bin\mysql") {
            $dirs = Get-ChildItem "D:\laragon\bin\mysql\mysql-*" | Sort-Object Name -Descending
            foreach ($d in $dirs) {
                if (Test-Path "$($d.FullName)\bin\mysqld.exe") {
                    $mysqlPath = "$($d.FullName)\bin\mysqld.exe"
                    break
                }
            }
        }
        # Try Laragon C:
        if (-not $mysqlPath -and (Test-Path "C:\laragon\bin\mysql")) {
            $dirs = Get-ChildItem "C:\laragon\bin\mysql\mysql-*" | Sort-Object Name -Descending
            foreach ($d in $dirs) {
                if (Test-Path "$($d.FullName)\bin\mysqld.exe") {
                    $mysqlPath = "$($d.FullName)\bin\mysqld.exe"
                    break
                }
            }
        }
        # Try XAMPP C:
        if (-not $mysqlPath -and (Test-Path "C:\xampp\mysql\bin\mysqld.exe")) {
            $mysqlPath = "C:\xampp\mysql\bin\mysqld.exe"
        }
    }

    $mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if ($mysqlRunning) {
        $iconStep2.Text = [char]0x2705
        $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
        $textStep2.Text = $msg.step2_running
        $textStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
    } else {
        if ($mysqlPath) {
            Set-Status 45 $msg.step2_starting
            Start-Process $mysqlPath -ArgumentList "--console" -WindowStyle Hidden
            # Wait for MySQL bootup
            Start-Sleep -Seconds 4
            $mysqlRunningNow = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
            if ($mysqlRunningNow) {
                $iconStep2.Text = [char]0x2705
                $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
                $textStep2.Text = $msg.step2_success
                $textStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
            } else {
                $iconStep2.Text = [char]0x26A0
                $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(245, 158, 11))
                $textStep2.Text = $msg.step2_fail
            }
        } else {
            $iconStep2.Text = [char]0x26A0
            $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(245, 158, 11))
            $textStep2.Text = $msg.step2_no_dir
        }
    }

    # --- Step 3: Start PHP Web Server ---
    $iconStep3.Text = [char]0x23F3
    $iconStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep3.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 70 $msg.step3_running
    Start-Sleep -Milliseconds 600

    try {
        # Launch PHP built-in web server in hidden mode
        $global:phpProcess = Start-Process $phpPath -ArgumentList "-S localhost:8000" -PassThru -WindowStyle Hidden
        Start-Sleep -Milliseconds 500
        
        $iconStep3.Text = [char]0x2705
        $iconStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
        $textStep3.Text = $msg.step3_success
        $textStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
    } catch {
        $iconStep3.Text = [char]0x274C
        $iconStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $textStep3.Text = $msg.step3_fail
        Set-Status 0 $msg.status_server_fail
        $btnAction.Content = $msg.btn_close
        $btnAction.Foreground = [System.Windows.Media.Brushes]::White
        $btnAction.Background = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $btnAction.IsEnabled = $true
        return
    }

    # --- Step 4: Open Web Browser ---
    $iconStep4.Text = [char]0x23F3
    $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep4.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 90 $msg.step4_running
    Start-Sleep -Milliseconds 600

    Start-Process "http://localhost:8000"
    $iconStep4.Text = [char]0x2705
    $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
    $textStep4.Text = $msg.step4_success
    $textStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))

    # --- Done ---
    Set-Status 100 $msg.status_done
    
    $btnAction.Content = $msg.btn_stop_server
    $btnAction.Foreground = [System.Windows.Media.Brushes]::White
    $btnAction.Background = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
    $btnAction.IsEnabled = $true
})

# Show the GUI dialog (blocks until closed)
[void]$window.ShowDialog()
