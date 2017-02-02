mod.wizards {
    newContentElement {
        wizardItems {
            dataviewer {
                header = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer
                elements {
                    content {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Images/logo_dataviewer.png
                        title = MageDeveloper DataViewer Plugins
                        description = _______________________________________________________________
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_record
                        }
                    }
                    tx_dataviewer_record {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_record.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi1
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi1_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_record
                        }
                    }
                    tx_dataviewer_search {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_search.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi2
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi2_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_search
                        }
                    }
                    tx_dataviewer_letter {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_letter.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi3
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi3_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_letter
                        }
                    }
                    tx_dataviewer_sort {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_sort.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi4
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi4_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_sort
                        }
                    }
                    tx_dataviewer_filter {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_filter.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi5
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi5_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_filter
                        }
                    }
                    tx_dataviewer_select {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_select.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi6
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi6_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_select
                        }
                    }
                    tx_dataviewer_form {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_form.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi7
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi7_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_form
                        }
                    }
                    tx_dataviewer_pager {
                        icon = ../typo3conf/ext/dataviewer/Resources/Public/Icons/Plugins/dataviewer_pager.gif
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi8
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi8_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_pager
                        }
                    }                    
					
                }
                show = *
            }
        }
    }
}
