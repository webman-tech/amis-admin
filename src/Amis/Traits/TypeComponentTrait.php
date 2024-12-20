<?php

namespace WebmanTech\AmisAdmin\Amis\Traits;

use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Amis\ComponentMaker;
use WebmanTech\AmisAdmin\Helper\ContainerHelper;

/**
 * 以下方法通过 cli-app 的 amis:generate-type 生成
 *
 * amis 组件
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/index
 *
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeAction(array $schema = []) Action 行为按钮    https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeAlert(array $schema = []) Alert 提示    https://aisuda.bce.baidu.com/amis/zh-CN/components/alert
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeAmis(array $schema = []) AMIS 渲染器    https://aisuda.bce.baidu.com/amis/zh-CN/components/amis
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeAnchorNav(array $schema = []) AnchorNav 锚点导航    https://aisuda.bce.baidu.com/amis/zh-CN/components/anchor-nav
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeApp(array $schema = []) App 多页应用    https://aisuda.bce.baidu.com/amis/zh-CN/components/app
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeAudio(array $schema = []) Audio 音频    https://aisuda.bce.baidu.com/amis/zh-CN/components/audio
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeAvatar(array $schema = []) Avatar 头像    https://aisuda.bce.baidu.com/amis/zh-CN/components/avatar
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeBadge(array $schema = []) Badge 角标    https://aisuda.bce.baidu.com/amis/zh-CN/components/badge
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeBarcode(array $schema = []) BarCode 条形码    https://aisuda.bce.baidu.com/amis/zh-CN/components/barcode
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeBreadcrumb(array $schema = []) Breadcrumb 面包屑    https://aisuda.bce.baidu.com/amis/zh-CN/components/breadcrumb
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeButton(array $schema = []) Button 按钮    https://aisuda.bce.baidu.com/amis/zh-CN/components/button
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeButtonGroup(array $schema = []) ButtonGroup 按钮组    https://aisuda.bce.baidu.com/amis/zh-CN/components/button-group
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCalendar(array $schema = []) Calendar 日历日程    https://aisuda.bce.baidu.com/amis/zh-CN/components/calendar
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCard(array $schema = []) Card 卡片    https://aisuda.bce.baidu.com/amis/zh-CN/components/card
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCards(array $schema = []) Cards 卡片组    https://aisuda.bce.baidu.com/amis/zh-CN/components/cards
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCarousel(array $schema = []) Carousel 轮播图    https://aisuda.bce.baidu.com/amis/zh-CN/components/carousel
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeChart(array $schema = []) Chart 图表    https://aisuda.bce.baidu.com/amis/zh-CN/components/chart
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCode(array $schema = []) Code 代码高亮    https://aisuda.bce.baidu.com/amis/zh-CN/components/code
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCollapse(array $schema = []) Collapse 折叠器    https://aisuda.bce.baidu.com/amis/zh-CN/components/collapse
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeColor(array $schema = []) Color 颜色    https://aisuda.bce.baidu.com/amis/zh-CN/components/color
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeContainer(array $schema = []) Container 容器    https://aisuda.bce.baidu.com/amis/zh-CN/components/container
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCrud(array $schema = []) CRUD 增删改查    https://aisuda.bce.baidu.com/amis/zh-CN/components/crud
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeCustom(array $schema = []) Custom 自定义组件    https://aisuda.bce.baidu.com/amis/zh-CN/components/custom
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeDate(array $schema = []) Date 日期时间    https://aisuda.bce.baidu.com/amis/zh-CN/components/date
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeDialog(array $schema = []) Dialog 对话框    https://aisuda.bce.baidu.com/amis/zh-CN/components/dialog
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeDivider(array $schema = []) Divider 分割线    https://aisuda.bce.baidu.com/amis/zh-CN/components/divider
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeDrawer(array $schema = []) Drawer 抽屉    https://aisuda.bce.baidu.com/amis/zh-CN/components/drawer
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeDropdownButton(array $schema = []) DropDownButton 下拉菜单    https://aisuda.bce.baidu.com/amis/zh-CN/components/dropdown-button
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeEach(array $schema = []) Each 循环渲染器    https://aisuda.bce.baidu.com/amis/zh-CN/components/each
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeFlex(array $schema = []) Flex 布局    https://aisuda.bce.baidu.com/amis/zh-CN/components/flex
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeForm(array $schema = []) Form 表单    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/index
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeGrid(array $schema = []) Grid 水平分栏    https://aisuda.bce.baidu.com/amis/zh-CN/components/grid
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeGrid2d(array $schema = []) Grid 2D 布局    https://aisuda.bce.baidu.com/amis/zh-CN/components/grid-2d
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeGridNav(array $schema = []) GridNav 宫格导航    https://aisuda.bce.baidu.com/amis/zh-CN/components/grid-nav
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeHbox(array $schema = []) HBox 布局    https://aisuda.bce.baidu.com/amis/zh-CN/components/hbox
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeHtml(array $schema = []) Html    https://aisuda.bce.baidu.com/amis/zh-CN/components/html
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeIcon(array $schema = []) Icon 图标    https://aisuda.bce.baidu.com/amis/zh-CN/components/icon
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeIframe(array $schema = []) iFrame    https://aisuda.bce.baidu.com/amis/zh-CN/components/iframe
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeImage(array $schema = []) Image 图片    https://aisuda.bce.baidu.com/amis/zh-CN/components/image
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeImages(array $schema = []) Images 图片集    https://aisuda.bce.baidu.com/amis/zh-CN/components/images
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeJson(array $schema = []) Json    https://aisuda.bce.baidu.com/amis/zh-CN/components/json
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeLink(array $schema = []) Link 链接    https://aisuda.bce.baidu.com/amis/zh-CN/components/link
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeList(array $schema = []) List 列表    https://aisuda.bce.baidu.com/amis/zh-CN/components/list
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeLog(array $schema = []) Log 实时日志    https://aisuda.bce.baidu.com/amis/zh-CN/components/log
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeMapping(array $schema = []) Mapping 映射    https://aisuda.bce.baidu.com/amis/zh-CN/components/mapping
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeMarkdown(array $schema = []) Markdown 渲染    https://aisuda.bce.baidu.com/amis/zh-CN/components/markdown
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeNav(array $schema = []) Nav 导航    https://aisuda.bce.baidu.com/amis/zh-CN/components/nav
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeNumber(array $schema = []) Number 数字展示    https://aisuda.bce.baidu.com/amis/zh-CN/components/number
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeOfficeViewer(array $schema = []) Office Viewer    https://aisuda.bce.baidu.com/amis/zh-CN/components/office-viewer
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeOfficeViewerExcel(array $schema = []) Office Viewer Excel    https://aisuda.bce.baidu.com/amis/zh-CN/components/office-viewer-excel
 * @method static \WebmanTech\AmisAdmin\Amis\Page typePage(array $schema = []) Page 页面    https://aisuda.bce.baidu.com/amis/zh-CN/components/page
 * @method static \WebmanTech\AmisAdmin\Amis\Component typePagination(array $schema = []) Pagination 分页组件    https://aisuda.bce.baidu.com/amis/zh-CN/components/pagination
 * @method static \WebmanTech\AmisAdmin\Amis\Component typePaginationWrapper(array $schema = []) PaginationWrapper 分页容器    https://aisuda.bce.baidu.com/amis/zh-CN/components/pagination-wrapper
 * @method static \WebmanTech\AmisAdmin\Amis\Component typePanel(array $schema = []) Panel 面板    https://aisuda.bce.baidu.com/amis/zh-CN/components/panel
 * @method static \WebmanTech\AmisAdmin\Amis\Component typePdfViewer(array $schema = []) PDF Viewer    https://aisuda.bce.baidu.com/amis/zh-CN/components/pdf-viewer
 * @method static \WebmanTech\AmisAdmin\Amis\Component typePopover(array $schema = []) PopOver 弹出提示    https://aisuda.bce.baidu.com/amis/zh-CN/components/popover
 * @method static \WebmanTech\AmisAdmin\Amis\Component typePortlet(array $schema = []) Portlet 门户栏目    https://aisuda.bce.baidu.com/amis/zh-CN/components/portlet
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeProgress(array $schema = []) Progress 进度条    https://aisuda.bce.baidu.com/amis/zh-CN/components/progress
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeProperty(array $schema = []) Property 属性表    https://aisuda.bce.baidu.com/amis/zh-CN/components/property
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeQrcode(array $schema = []) QRCode 二维码    https://aisuda.bce.baidu.com/amis/zh-CN/components/qrcode
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeRemark(array $schema = []) Remark 标记    https://aisuda.bce.baidu.com/amis/zh-CN/components/remark
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeSearchBox(array $schema = []) Search Box 搜索框    https://aisuda.bce.baidu.com/amis/zh-CN/components/search-box
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeService(array $schema = []) Service 功能型容器    https://aisuda.bce.baidu.com/amis/zh-CN/components/service
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeShape(array $schema = []) Shape 形状    https://aisuda.bce.baidu.com/amis/zh-CN/components/shape
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeSparkline(array $schema = []) Sparkline 走势图    https://aisuda.bce.baidu.com/amis/zh-CN/components/sparkline
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeSpinner(array $schema = []) Spinner 加载中    https://aisuda.bce.baidu.com/amis/zh-CN/components/spinner
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeStatus(array $schema = []) Status 状态    https://aisuda.bce.baidu.com/amis/zh-CN/components/status
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeSteps(array $schema = []) Steps 步骤条    https://aisuda.bce.baidu.com/amis/zh-CN/components/steps
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeSwitchContainer(array $schema = []) switch-container 状态容器    https://aisuda.bce.baidu.com/amis/zh-CN/components/switch-container
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTable(array $schema = []) Table 表格    https://aisuda.bce.baidu.com/amis/zh-CN/components/table
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTableView(array $schema = []) Table View 表格展现    https://aisuda.bce.baidu.com/amis/zh-CN/components/table-view
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTable2(array $schema = []) Table2 表格    https://aisuda.bce.baidu.com/amis/zh-CN/components/table2
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTabs(array $schema = []) Tabs 选项卡    https://aisuda.bce.baidu.com/amis/zh-CN/components/tabs
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTag(array $schema = []) Tag 标签    https://aisuda.bce.baidu.com/amis/zh-CN/components/tag
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTasks(array $schema = []) Tasks 任务操作集合    https://aisuda.bce.baidu.com/amis/zh-CN/components/tasks
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTimeline(array $schema = []) Timeline 时间轴    https://aisuda.bce.baidu.com/amis/zh-CN/components/timeline
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeToast(array $schema = []) Toast 轻提示    https://aisuda.bce.baidu.com/amis/zh-CN/components/toast
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTooltipWrapper(array $schema = []) TooltipWrapper 文字提示容器    https://aisuda.bce.baidu.com/amis/zh-CN/components/tooltip-wrapper
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeTpl(array $schema = []) Tpl 模板    https://aisuda.bce.baidu.com/amis/zh-CN/components/tpl
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeVideo(array $schema = []) Video 视频    https://aisuda.bce.baidu.com/amis/zh-CN/components/video
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeWebComponent(array $schema = []) Web Component    https://aisuda.bce.baidu.com/amis/zh-CN/components/web-component
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeWizard(array $schema = []) Wizard 向导    https://aisuda.bce.baidu.com/amis/zh-CN/components/wizard
 * @method static \WebmanTech\AmisAdmin\Amis\Component typeWrapper(array $schema = []) Wrapper 包裹容器    https://aisuda.bce.baidu.com/amis/zh-CN/components/wrapper
 *
 * amis 表单
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeButtonGroupSelect(array $schema = []) Button-Group-Select 按钮点选    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/button-group-select
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeButtonToolbar(array $schema = []) Button-Toolbar 按钮工具栏    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/button-toolbar
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeChainSelect(array $schema = []) Chained-Select 链式下拉框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/chain-select
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeChartRadios(array $schema = []) ChartRadios 图表单选框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/chart-radios
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeCheckbox(array $schema = []) Checkbox 勾选框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/checkbox
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeCheckboxes(array $schema = []) Checkboxes 复选框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/checkboxes
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeCombo(array $schema = []) Combo 组合    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/combo
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeConditionBuilder(array $schema = []) 组合条件    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/condition-builder
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeControl(array $schema = []) Control 表单项包裹    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/control
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeDiffEditor(array $schema = []) DiffEditor 对比编辑器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/diff-editor
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeEditor(array $schema = []) Editor 编辑器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/editor
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeFieldset(array $schema = []) FieldSet 表单项集合    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/fieldset
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeFormula(array $schema = []) Formula 公式    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/formula
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeGroup(array $schema = []) Group 表单项组    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/group
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeHidden(array $schema = []) Hidden 隐藏字段    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/hidden
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputArray(array $schema = []) InputArray 数组输入框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-array
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputCity(array $schema = []) InputCity 城市选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-city
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputColor(array $schema = []) InputColor 颜色选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-color
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputDate(array $schema = []) InputDate 日期    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-date
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputDateRange(array $schema = []) InputDateRange 日期范围    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-date-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputDatetime(array $schema = []) InputDatetime 日期时间    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-datetime
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputDatetimeRange(array $schema = []) InputDatetimeRange 日期时间范围    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-datetime-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputExcel(array $schema = []) InputExcel 解析 Excel    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-excel
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputFile(array $schema = []) InputFile 文件上传    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-file
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputFormula(array $schema = []) InputFormula 公式编辑器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-formula
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputGroup(array $schema = []) Input-Group 输入框组合    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-group
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputImage(array $schema = []) InputImage 图片    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-image
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputKv(array $schema = []) InputKV 键值对    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-kv
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputKvs(array $schema = []) InputKVS 键值对象    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-kvs
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputMonth(array $schema = []) InputMonth 月份    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-month
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputMonthRange(array $schema = []) InputMonthRange 月份范围    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-month-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputNumber(array $schema = []) InputNumber 数字输入框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-number
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputPassword(array $schema = []) InputPassword 密码输入框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-password
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputQuarter(array $schema = []) InputQuarter 季度    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-quarter
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputQuarterRange(array $schema = []) InputQuarterRange 季度范围    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-quarter-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputRange(array $schema = []) InputRange 滑块    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputRating(array $schema = []) InputRating 评分    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-rating
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputRepeat(array $schema = []) InputRepeat 重复频率选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-repeat
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputRichText(array $schema = []) InputRichText 富文本编辑器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-rich-text
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputSignature(array $schema = []) inputSignature 签名面板    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-signature
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputSubForm(array $schema = []) InputSubForm 子表单    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-sub-form
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputTable(array $schema = []) InputTable 表格    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-table
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputTag(array $schema = []) InputTag 标签选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-tag
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputText(array $schema = []) InputText 输入框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-text
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputTime(array $schema = []) InputTime 时间    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-time
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputTimeRange(array $schema = []) InputTimeRange 时间范围    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-time-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputTree(array $schema = []) InputTree 树形选择框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-tree
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputVerificationCode(array $schema = []) 验证码输入 InputVerificationCode    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-verification-code
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputYear(array $schema = []) Year 年份选择    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-year
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeInputYearRange(array $schema = []) InputYearRange 年份范围    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-year-range
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeJsonSchema(array $schema = []) JSONSchema    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/json-schema
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeJsonSchemaEditor(array $schema = []) JSONSchema Editor    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/json-schema-editor
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeListSelect(array $schema = []) ListSelect 列表    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/list-select
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeLocationPicker(array $schema = []) LocationPicker 地理位置    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/location-picker
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeMatrixCheckboxes(array $schema = []) MatrixCheckboxes 矩阵    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/matrix-checkboxes
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeNestedSelect(array $schema = []) NestedSelect 级联选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/nestedselect
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeOptions(array $schema = []) Options 选择器表单项    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/options
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typePicker(array $schema = []) Picker 列表选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/picker
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeRadio(array $schema = []) Radio 单选框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/radio
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeRadios(array $schema = []) Radios 单选框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/radios
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeSelect(array $schema = []) Select 选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/select
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeStatic(array $schema = []) Static 静态展示    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/static
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeSwitch(array $schema = []) Switch 开关    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/switch
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeTabsTransfer(array $schema = []) TabsTransfer 组合穿梭器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/tabs-transfer
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeTabsTransferPicker(array $schema = []) TabsTransferPicker 穿梭选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/tabs-transfer-picker
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeTextarea(array $schema = []) Textarea 多行文本输入框    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/textarea
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeTransfer(array $schema = []) Transfer 穿梭器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/transfer
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeTransferPicker(array $schema = []) TransferPicker 穿梭选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/transfer-picker
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeTreeSelect(array $schema = []) TreeSelect 树形选择器    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/treeselect
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeUuid(array $schema = []) UUID 字段    https://aisuda.bce.baidu.com/amis/zh-CN/components/form/uuid
 *
 * 自定义
 * @method static \WebmanTech\AmisAdmin\Amis\ActionButtons typeActionButtons(array $schema = [])     
 * @method static \WebmanTech\AmisAdmin\Amis\Crud typeCustomCrud(array $schema = [])     
 * @method static \WebmanTech\AmisAdmin\Amis\DetailAttribute typeDetailAttribute(array $schema = [])     
 * @method static \WebmanTech\AmisAdmin\Amis\FormField typeFormField(array $schema = [])     
 * @method static \WebmanTech\AmisAdmin\Amis\GridBatchActions typeGridBatchActions(array $schema = [])     
 * @method static \WebmanTech\AmisAdmin\Amis\GridColumn typeGridColumn(array $schema = [])     
 * @method static \WebmanTech\AmisAdmin\Amis\GridColumnActions typeGridColumnActions(array $schema = [])     
 */
trait TypeComponentTrait
{
    /**
     * 是否在调用 typeXxx
     * @param string $name
     * @return bool
     */
    protected static function isCallType(string $name): bool
    {
        return strlen($name) > 4 && strpos($name, 'type') === 0;
    }

    /**
     * 调用 typeXxx 时，设置 type 配置
     * @param string $name
     * @param array $arguments
     * @return Component
     */
    protected static function callType(string $name, array $arguments): Component
    {
        return ContainerHelper::getSingleton(ComponentMaker::class)->{$name}(...$arguments);
    }
}