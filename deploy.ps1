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
    btn_close = Get-Thai "4Lib4Li04LiU4Lir4LiZ4LmJ4Liy4LiV4LmI4Liy4LiH"
    btn_uploading = Get-Thai "4LiB4Liz4Lil4Lix4LiH4Lit4Lix4Lib4LmC4Lir4Lil4LiU4Lij4Liw4Lia4LiaLi4u"
    msg_start = Get-Thai "PT09IOC5gOC4o+C4tOC5iOC4oeC4leC5ieC4meC4guC4muC4p+C4meC4geC4suC4oyBEZXBsb3kgPT09"
    step1_run = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LiV4Lij4Lin4LiI4Liq4Lit4Lia4LmC4Lih4LiU4Li54LilIEdpdC4uLg=="
    step1_fail = Get-Thai "4LmE4Lih4LmI4Lie4LiaIEdpdCDguYPguJnguYDguITguKPguLfguYjguK3guIchIOC4geC4o+C4uOC4k+C4suC4leC4tOC4lOC4leC4seC5ieC4hyBHaXQ="
    log_no_git = Get-Thai "W0VSUl0g4LmE4Lih4LmI4Lie4Lia4LiE4Liz4Liq4Lix4LmI4LiHIGdpdCDguYPguJkgUEFUSCDguILguK3guIfguKPguLDguJrguJo="
    status_no_git = Get-Thai "4Lil4LmJ4Lih4LmA4Lir4Lil4LinOiDguYTguKHguYjguJ7guJrguYLguJvguKPguYHguIHguKPguKEgR2l0"
    step1_success = Get-Thai "4Lie4Lia4LiE4Liz4Liq4Lix4LmI4LiHIEdpdCDguYDguKPguLXguKLguJrguKPguYnguK3guKI="
    log_found_git = Get-Thai "W0lORk9dIOC4leC4o+C4p+C4iOC4nuC4miBHaXQg4LmD4LiZ4LmA4LiE4Lij4Li34LmI4Lit4LiH4LmA4Lij4Li14Lii4Lia4Lij4LmJ4Lit4Lii"
    step2_run = Get-Thai "4LiB4Liz4Lil4Lix4LiH4Lia4Lix4LiZ4LiX4Li24LiB4Lib4Lij4Liw4Lin4Lix4LiV4Li04LiB4Liy4Lij4LmA4Lib4Lil4Li14LmI4Lii4LiZ4LmB4Lib4Lil4LiH4LmC4LiE4LmJ4LiULi4u"
    log_add_files = Get-Thai "W0dpdF0g4LiB4Liz4Lil4Lix4LiH4LmB4Lit4LiU4LmE4Lif4Lil4LmM4LmD4Lir4Lih4LmI4LmB4Lil4Liw4LmB4LiB4LmJ4LmE4LiCLi4u"
    log_commit_msg = Get-Thai "W0dpdF0g4LiB4Liz4Lil4Lix4LiH4LiX4LizIENvbW1pdCAo4LiC4LmJ4Lit4LiE4Lin4Liy4LihOiA="
    step2_success = Get-Thai "4Lia4Lix4LiZ4LiX4Li24LiB4Lib4Lij4Liw4Lin4Lix4LiV4Li04LiB4Liy4Lij4LmB4LiB4LmJ4LmE4LiC4Liq4Liz4LmA4Lij4LmH4LiIIChHaXQgQ29tbWl0KQ=="
    log_commit_success = Get-Thai "W0lORk9dIOC4muC4seC4meC4l+C4tuC4geC4geC4suC4o+C5gOC4m+C4peC4teC5iOC4ouC4meC5geC4m+C4peC4hyAoR2l0IENvbW1pdCkg4Liq4Liz4LmA4Lij4LmH4LiI"
    step3_run = Get-Thai "4LiB4Liz4Lil4Lix4LiH4Liq4LmI4LiH4LmE4Lif4Lil4LmM4LmE4Lib4Lii4Lix4LiHIEdpdEh1YiAoR2l0IFB1c2gpLi4u"
    log_push_start = Get-Thai "W0dpdF0g4LiB4Liz4Lil4Lix4LiH4Lit4Lix4Lib4LmC4Lir4Lil4LiU4LiC4LmJ4Lit4Lih4Li54Lil4LmE4Lib4Lii4Lix4LiHIEdpdEh1YiAoZ2l0IHB1c2ggb3JpZ2luIG1haW4pLi4u"
    step3_fail = Get-Thai "4Lit4Lix4Lib4LmC4Lir4Lil4LiU4LmE4Lib4Lii4Lix4LiHIEdpdEh1YiDguKXguYnguKHguYDguKvguKXguKch"
    log_push_fail = Get-Thai "W0VSUl0g4Lit4Lix4Lib4LmC4Lir4Lil4LiU4LiC4LmJ4Lit4Lih4Li54Lil4LmE4Lib4Lii4Lix4LiHIEdpdEh1YiDguYTguKHguYjguKrguLPguYDguKPguYfguIghIOC4leC4o+C4p+C4iOC4quC4reC4muC4quC4tOC4l+C4mOC4tOC5jOC5geC4peC4sOC4geC4suC4o+C5gOC4iuC4t+C5iOC4reC4oeC4leC5iOC4reC4reC4tOC4meC5gOC4l+C4reC4o+C5jOC5gOC4meC5h+C4lQ=="
    status_push_fail = Get-Thai "4Lil4LmJ4Lih4LmA4Lir4Lil4LinOiBHaXQgUHVzaCDguKXguYnguKHguYDguKvguKXguKc="
    step3_success = Get-Thai "4Liq4LmI4LiH4LmC4LiE4LmJ4LiU4LmE4Lib4Lii4Lix4LiHIEdpdEh1YiDguKrguLPguYDguKPguYfguIjguYDguKPguLXguKLguJrguKPguYnguK3guKIh"
    log_push_success = Get-Thai "W0lORk9dIOC4reC4seC4m+C5guC4q+C4peC4lOC5hOC4m+C4ouC4seC4hyBHaXRIdWIgKEdpdCBQdXNoKSDguYDguKPguLXguKLguJrguKPguYnguK3guKLguYHguKXguYnguKc="
    step4_run = Get-Thai "4LiB4Liz4Lil4Lix4LiH4LiV4Lij4Lin4LiI4LmA4LiK4LmH4LiEIFJhaWx3YXkgQ0xJLi4u"
    step4_no_cli = Get-Thai "4LmE4Lih4LmI4Lie4LiaIFJhaWx3YXkgQ0xJICjguIjguLDguJbguLnguIHguJTguLbguIfguILguYnguK3guKHguLnguKXguK3guLHguJXguYLguJnguKHguLHguJXguLTguIjguLLguIEgR2l0SHViKQ=="
    log_no_railway = Get-Thai "W1dBUk5dIOC5hOC4oeC5iOC4nuC4miBSYWlsd2F5IENMSSDguYPguJnguKPguLDguJrguJohIOC5hOC4oeC5iOC4quC4suC4oeC4suC4o+C4luC4quC4seC5iOC4hyBEZXBsb3kg4LiV4Lij4LiH4LmGIOC5hOC4lOC5iQ=="
    log_railway_auto = Get-Thai "W0lORk9dIOC4l+C4seC5ieC4h+C4meC4teC5iSBSYWlsd2F5IOC4iOC4sOC4l+C4s+C4geC4suC4oyBBdXRvLURlcGxveSDguIjguLLguIEgR2l0SHViIFJlcG8g4LiX4Li14LmI4LmA4Lij4LiyIFB1c2gg4LmE4Lib4LmA4Lih4Li34LmI4Lit4LiE4Lij4Li54LmI4LmD4Lir4LmJ4LmA4Lit4LiH4Lit4Lix4LiV4LmC4LiZ4Lih4Lix4LiV4Li0"
    status_auto_deploy = Get-Thai "4LmA4Liq4Lij4LmH4LiI4Liq4Lih4Lia4Li54Lij4LiT4LmMISDguK3guLHguJvguYDguJTguJXguJzguYjguLLguJkgR2l0SHViIEF1dG8tRGVwbG95"
    step4_run_up = Get-Thai "4LiB4Liz4Lil4Lix4LiH4Liq4LmI4LiH4LmE4Lif4Lil4LmM4LmE4Lib4Lii4Lix4LiHIFJhaWx3YXkgKHJhaWx3YXkgdXApLi4u"
    log_railway_up = Get-Thai "W1JhaWx3YXldIOC4leC4o+C4p+C4iOC4nuC4muC4hOC4s+C4quC4seC5iOC4hyBSYWlsd2F5ISDguIHguLPguKXguLHguIfguKrguLHguYjguIcgRGVwbG95IOC4lOC5ieC4p+C4oiBSYWlsd2F5IENMSS4uLg=="
    step4_fail = Get-Thai "4LiB4Liy4LijIERlcGxveSDguILguLbguYnguJkgUmFpbHdheSDguKXguYnguKHguYDguKvguKXguKch"
    log_railway_fail = Get-Thai "W0VSUl0g4LiB4Liy4Lij4LmA4Lij4Li14Lii4LiB4LmD4LiK4LmJ4LiH4Liy4LiZIFJhaWx3YXkgQ0xJIOC5gOC4nuC4t+C5iOC4rSBkZXBsb3kg4LmE4Lih4LmI4Liq4Liz4LmA4Lij4LmH4LiI"
    status_railway_fail = Get-Thai "4Lil4LmJ4Lih4LmA4Lir4Lil4LinOiBSYWlsd2F5IERlcGxveSDguKXguYnguKHguYDguKvguKXguKc="
    step4_success = Get-Thai "4Liq4LmI4LiH4LmC4LiE4LmJ4LiU4LmB4Lil4Liw4Lij4Lix4LiZ4LiE4Lit4Lih4LmE4Lie4Lil4LmM4Lia4LiZIFJhaWx3YXkg4Liq4Liz4LmA4Lij4LmH4LiI"
    log_railway_success = Get-Thai "W0lORk9dIERlcGxveSDguILguLbguYnguJnguKPguLDguJrguJogUmFpbHdheSDguYDguKPguLXguKLguJrguKPguYnguK3guKLguYHguKXguYnguKch"
    status_success = Get-Thai "4LiB4Liy4LijIERlcGxveSDguKrguLPguYDguKPguYfguIjguYDguKPguLXguKLguJrguKPguYnguK3guKLguKrguKHguJrguLnguKPguJPguYwh"
    status_fail_general = Get-Thai "4LiB4Liy4LijIERlcGxveSDguKXguYnguKHguYDguKvguKXguKch"
}

$xaml = @'
<Window xmlns="http://schemas.microsoft.com/winfx/2006/xaml/presentation"
        xmlns:x="http://schemas.microsoft.com/winfx/2006/xaml"
        Title="Luang Prabang Heritage Deployment"
        Height="540" Width="520"
        WindowStartupLocation="CenterScreen"
        WindowStyle="None"
        AllowsTransparency="True"
        Background="Transparent"
        Topmost="True">
    <Border CornerRadius="16" Background="#0f172a" BorderBrush="#3b82f6" BorderThickness="2">
        <Grid Margin="24">
            <Grid.RowDefinitions>
                <RowDefinition Height="Auto"/> <!-- Header -->
                <RowDefinition Height="Auto"/> <!-- Input Commit Msg -->
                <RowDefinition Height="*"/>    <!-- Steps -->
                <RowDefinition Height="Auto"/> <!-- Log Console -->
                <RowDefinition Height="Auto"/> <!-- Progress -->
                <RowDefinition Height="Auto"/> <!-- Action Button -->
            </Grid.RowDefinitions>
            
            <!-- Header -->
            <StackPanel Grid.Row="0" Margin="0,0,0,12">
                <Grid>
                    <TextBlock Text="LUANG PRABANG HERITAGE DEPLOY" Foreground="#60a5fa" FontSize="18" FontWeight="Bold" HorizontalAlignment="Center"/>
                    <Button x:Name="BtnCloseTop" Content="&#x2715;" Width="24" Height="24" Background="Transparent" Foreground="#64748b" BorderThickness="0" FontWeight="Bold" FontSize="14" HorizontalAlignment="Right" VerticalAlignment="Top" Cursor="Hand"/>
                </Grid>
                <TextBlock Text="&#x0E23;&#x0E30;&#x0E1A;&#x0E1A;&#x0E2D;&#x0E31;&#x0E1B;&#x0E42;&#x0E2B;&#x0E25;&#x0E14;&#x0E41;&#x0E25;&#x0E30;&#x0E2D;&#x0E31;&#x0E1B;&#x0E40;&#x0E14;&#x0E15;&#x0E23;&#x0E30;&#x0E1A;&#x0E1A;&#x0E44;&#x0E1B;&#x0E22;&#x0E31;&#x0E07; GitHub &#x0E41;&#x0E25;&#x0E30; Railway" Foreground="#64748b" FontSize="11" HorizontalAlignment="Center" Margin="0,4,0,0"/>
            </StackPanel>
            
            <!-- Input Commit Msg -->
            <StackPanel x:Name="InputPanel" Grid.Row="1" Margin="10,0,10,12">
                <TextBlock Text="&#x0E02;&#x0E49;&#x0E2D;&#x0E04;&#x0E27;&#x0E32;&#x0E21;&#x0E1A;&#x0E31;&#x0E19;&#x0E17;&#x0E36;&#x0E01;&#x0E01;&#x0E32;&#x0E23;&#x0E2D;&#x0E31;&#x0E1B;&#x0E40;&#x0E14;&#x0E15; (Commit Message)" Foreground="#94a3b8" FontSize="12" Margin="0,0,0,6" FontWeight="SemiBold"/>
                <TextBox x:Name="TxtCommit" Height="32" Background="#1e293b" Foreground="White" BorderBrush="#3b82f6" BorderThickness="1.5" Padding="8,4" FontSize="13" VerticalContentAlignment="Center" VerticalAlignment="Center">
                    <TextBox.Resources>
                        <Style TargetType="Border">
                            <Setter Property="CornerRadius" Value="6"/>
                        </Style>
                    </TextBox.Resources>
                </TextBox>
            </StackPanel>
            
            <!-- Steps Panel -->
            <StackPanel Grid.Row="2" VerticalAlignment="Center" Margin="10,0,10,12">
                <!-- Step 1 -->
                <Grid Margin="0,4,0,4">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep1" Text="&#x25CB;" Foreground="#64748b" FontSize="13" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep1" Grid.Column="1" Text="&#x0E15;&#x0E23;&#x0E27;&#x0E08;&#x0E2A;&#x0E2D;&#x0E1A;&#x0E04;&#x0E27;&#x0E32;&#x0E21;&#x0E1E;&#x0E23;&#x0E49;&#x0E2D;&#x0E21;&#x0E02;&#x0E2D;&#x0E07; Git &#x0E43;&#x0E19;&#x0E40;&#x0E04;&#x0E23;&#x0E37;&#x0E48;&#x0E2D;&#x0E07;..." Foreground="#94a3b8" FontSize="12.5" VerticalAlignment="Center"/>
                </Grid>
                <!-- Step 2 -->
                <Grid Margin="0,4,0,4">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep2" Text="&#x25CB;" Foreground="#64748b" FontSize="13" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep2" Grid.Column="1" Text="&#x0E1A;&#x0E31;&#x0E19;&#x0E17;&#x0E36;&#x0E01;&#x0E01;&#x0E32;&#x0E23;&#x0E40;&#x0E1B;&#x0E25;&#x0E35;&#x0E48;&#x0E22;&#x0E19;&#x0E41;&#x0E1B;&#x0E25;&#x0E07;&#x0E42;&#x0E04;&#x0E49;&#x0E14; (Git Add &amp; Commit)..." Foreground="#94a3b8" FontSize="12.5" VerticalAlignment="Center"/>
                </Grid>
                <!-- Step 3 -->
                <Grid Margin="0,4,0,4">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep3" Text="&#x25CB;" Foreground="#64748b" FontSize="13" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep3" Grid.Column="1" Text="&#x0E2D;&#x0E31;&#x0E1B;&#x0E42;&#x0E2B;&#x0E25;&#x0E14;&#x0E42;&#x0E04;&#x0E49;&#x0E14;&#x0E44;&#x0E1B;&#x0E22;&#x0E31;&#x0E07; GitHub Repository..." Foreground="#94a3b8" FontSize="12.5" VerticalAlignment="Center"/>
                </Grid>
                <!-- Step 4 -->
                <Grid Margin="0,4,0,4">
                    <Grid.ColumnDefinitions>
                        <ColumnDefinition Width="35"/>
                        <ColumnDefinition Width="*"/>
                    </Grid.ColumnDefinitions>
                    <TextBlock x:Name="IconStep4" Text="&#x25CB;" Foreground="#64748b" FontSize="13" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    <TextBlock x:Name="TextStep4" Grid.Column="1" Text="&#x0E2D;&#x0E31;&#x0E1B;&#x0E42;&#x0E2B;&#x0E25;&#x0E14;&#x0E23;&#x0E30;&#x0E1A;&#x0E1A;&#x0E02;&#x0E36;&#x0E49;&#x0E19; Railway Cloud Hosting..." Foreground="#94a3b8" FontSize="12.5" VerticalAlignment="Center"/>
                </Grid>
            </StackPanel>
            
            <!-- Log Console -->
            <Grid Grid.Row="3" Margin="10,0,10,12">
                <Border CornerRadius="8" Background="#020617" BorderBrush="#1e293b" BorderThickness="1">
                    <TextBox x:Name="TxtLog" Height="130" TextWrapping="Wrap" VerticalScrollBarVisibility="Auto" Background="Transparent" Foreground="#38bdf8" FontFamily="Consolas" FontSize="11" IsReadOnly="True" BorderThickness="0" Padding="8"/>
                </Border>
            </Grid>
            
            <!-- Status & Progress -->
            <StackPanel Grid.Row="4" Margin="10,0,10,16">
                <ProgressBar x:Name="ProgressBar" Height="6" Minimum="0" Maximum="100" Value="0" Background="#1e293b" Foreground="#3b82f6" BorderThickness="0" Margin="0,0,0,8"/>
                <TextBlock x:Name="StatusText" Text="&#x0E01;&#x0E23;&#x0E38;&#x0E13;&#x0E32;&#x0E23;&#x0E30;&#x0E1A;&#x0E38; Commit Message &#x0E41;&#x0E25;&#x0E49;&#x0E27;&#x0E01;&#x0E14;&#x0E1B;&#x0E38;&#x0E48;&#x0E21;&#x0E40;&#x0E23;&#x0E34;&#x0E48;&#x0E21;&#x0E15;&#x0E49;&#x0E19;..." Foreground="#64748b" FontSize="12" FontWeight="SemiBold" HorizontalAlignment="Center"/>
            </StackPanel>
            
            <!-- Action Button -->
            <Grid Grid.Row="5">
                <Button x:Name="BtnAction" Content="&#x0E40;&#x0E23;&#x0E34;&#x0E48;&#x0E21;&#x0E01;&#x0E32;&#x0E23;&#x0E2D;&#x0E31;&#x0E1B;&#x0E42;&#x0E2B;&#x0E25;&#x0E14; (Deploy Now)" Height="36" Width="200" Background="#3b82f6" Foreground="White" BorderThickness="0" FontWeight="Bold" FontSize="13" HorizontalAlignment="Center" Cursor="Hand">
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
$txtCommit = $window.FindName("TxtCommit")
$txtLog = $window.FindName("TxtLog")

# Set Default Commit Message
$txtCommit.Text = "Update system features and configuration"

# Support window drag
$window.Add_MouseLeftButtonDown({
    $this.DragMove()
})

# Close top button event
$btnCloseTop.Add_Click({
    $window.Close()
})

# Log writer helper
function Append-Log {
    param([string]$text)
    $txtLog.AppendText($text + "`r`n")
    $txtLog.ScrollToEnd()
    Do-Events
}

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

$btnAction.Add_Click({
    # If done, button acts as Close
    if ($btnAction.Content -eq $msg.btn_close) {
        $window.Close()
        return
    }
    
    # Disable inputs & buttons to prevent double-click
    $txtCommit.IsEnabled = $false
    $btnAction.IsEnabled = $false
    $btnAction.Content = $msg.btn_uploading
    $btnAction.Background = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(30, 41, 59))
    $btnAction.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(100, 116, 139))
    
    $commitMsg = $txtCommit.Text.Trim()
    if ($commitMsg -eq "") {
        $commitMsg = "Update system features and configuration"
    }
    
    # Start the deployment process
    Run-Deploy -CommitMessage $commitMsg
})

function Run-Deploy {
    param([string]$CommitMessage)
    
    # Clear logs
    $txtLog.Clear()
    Append-Log $msg.msg_start
    
    # --- Step 1: Check Git ---
    $iconStep1.Text = [char]0x23F3
    $iconStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep1.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 10 $msg.step1_run
    
    $checkGit = Get-Command git -ErrorAction SilentlyContinue
    if (-not $checkGit) {
        $iconStep1.Text = [char]0x274C
        $iconStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $textStep1.Text = $msg.step1_fail
        Append-Log $msg.log_no_git
        Finish-Deploy $false $msg.status_no_git
        return
    }
    
    $iconStep1.Text = [char]0x2705
    $iconStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
    $textStep1.Text = $msg.step1_success
    $textStep1.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
    Append-Log $msg.log_found_git
    Start-Sleep -Milliseconds 400
    
    # --- Step 2: Git Add & Commit ---
    $iconStep2.Text = [char]0x23F3
    $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep2.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 30 $msg.step2_run
    
    Append-Log $msg.log_add_files
    git add . 2>&1 | ForEach-Object { Append-Log $_; Do-Events }
    
    $logMsgCommit = $msg.log_commit_msg + '"' + $CommitMessage + '"...'
    Append-Log $logMsgCommit
    git commit -m $CommitMessage 2>&1 | ForEach-Object { Append-Log $_; Do-Events }
    
    $iconStep2.Text = [char]0x2705
    $iconStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
    $textStep2.Text = $msg.step2_success
    $textStep2.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
    Append-Log $msg.log_commit_success
    Start-Sleep -Milliseconds 400
    
    # --- Step 3: Git Push to GitHub ---
    $iconStep3.Text = [char]0x23F3
    $iconStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep3.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 55 $msg.step3_run
    Append-Log $msg.log_push_start
    
    # Capture push output line-by-line
    $gitPushFailed = $false
    git push origin main 2>&1 | ForEach-Object {
        $line = $_.ToString()
        Append-Log $line
        if ($line -match "fatal:" -or $line -match "error:") {
            $gitPushFailed = $true
        }
        Do-Events
    }
    
    if ($gitPushFailed -or $LASTEXITCODE -ne 0) {
        $iconStep3.Text = [char]0x274C
        $iconStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $textStep3.Text = $msg.step3_fail
        Append-Log $msg.log_push_fail
        Finish-Deploy $false $msg.status_push_fail
        return
    }
    
    $iconStep3.Text = [char]0x2705
    $iconStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
    $textStep3.Text = $msg.step3_success
    $textStep3.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
    Append-Log $msg.log_push_success
    Start-Sleep -Milliseconds 400
    
    # --- Step 4: Railway Deploy ---
    $iconStep4.Text = [char]0x23F3
    $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(96, 165, 250))
    $textStep4.Foreground = [System.Windows.Media.Brushes]::White
    Set-Status 80 $msg.step4_run
    
    $checkRailway = Get-Command railway -ErrorAction SilentlyContinue
    if (-not $checkRailway) {
        $iconStep4.Text = [char]0x26A0
        $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(245, 158, 11))
        $textStep4.Text = $msg.step4_no_cli
        $textStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
        Append-Log $msg.log_no_railway
        Append-Log $msg.log_railway_auto
        Start-Sleep -Seconds 2
        $iconStep4.Text = [char]0x2705
        $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
        Finish-Deploy $true $msg.status_auto_deploy
        return
    }
    
    Set-Status 85 $msg.step4_run_up
    Append-Log $msg.log_railway_up
    
    $railwayFailed = $false
    railway up --detach 2>&1 | ForEach-Object {
        $line = $_.ToString()
        Append-Log $line
        if ($line -match "Error:" -or $line -match "failed") {
            $railwayFailed = $true
        }
        Do-Events
    }
    
    if ($railwayFailed -or $LASTEXITCODE -ne 0) {
        $iconStep4.Text = [char]0x274C
        $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $textStep4.Text = $msg.step4_fail
        Append-Log $msg.log_railway_fail
        Finish-Deploy $false $msg.status_railway_fail
        return
    }
    
    $iconStep4.Text = [char]0x2705
    $iconStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
    $textStep4.Text = $msg.step4_success
    $textStep4.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(148, 163, 184))
    Append-Log $msg.log_railway_success
    
    Finish-Deploy $true $msg.status_success
}

function Finish-Deploy {
    param(
        [bool]$Success,
        [string]$Message
    )
    
    if ($Success) {
        Set-Status 100 $Message
        $statusText.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
        $btnAction.Background = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(16, 185, 129))
    } else {
        Set-Status 0 $msg.status_fail_general
        $statusText.Text = $Message
        $statusText.Foreground = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
        $btnAction.Background = New-Object System.Windows.Media.SolidColorBrush([System.Windows.Media.Color]::FromRgb(239, 68, 68))
    }
    
    $btnAction.Content = $msg.btn_close
    $btnAction.Foreground = [System.Windows.Media.Brushes]::White
    $btnAction.IsEnabled = $true
}

# Show the GUI dialog
[void]$window.ShowDialog()
