mod.wizards {
    newContentElement {
        wizardItems {
            dataviewer {
                header = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer
                elements {
                    content {
                        iconIdentifier = dataviewer-icon-logo
                        title = MageDeveloper DataViewer Plugins
                        description = _______________________________________________________________
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_record
                        }
                    }
                    tx_dataviewer_record {
                        iconIdentifier = dataviewer-icon-dataviewer_record
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi1
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi1_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_record
                        }
                    }
                    tx_dataviewer_search {
                        iconIdentifier = dataviewer-icon-dataviewer_search
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi2
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi2_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_search
                        }
                    }
                    tx_dataviewer_letter {
                        iconIdentifier = dataviewer-icon-dataviewer_letter
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi3
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi3_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_letter
                        }
                    }
                    tx_dataviewer_sort {
                        iconIdentifier = dataviewer-icon-dataviewer_sort
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi4
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi4_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_sort
                        }
                    }
                    tx_dataviewer_filter {
                        iconIdentifier = dataviewer-icon-dataviewer_filter
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi5
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi5_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_filter
                        }
                    }
                    tx_dataviewer_select {
                        iconIdentifier = dataviewer-icon-dataviewer_select
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi6
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi6_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_select
                        }
                    }
                    tx_dataviewer_form {
                        iconIdentifier = dataviewer-icon-dataviewer_form
                        title = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi7
                        description = LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi7_description
                        tt_content_defValues {
                            CType = list
                            list_type = dataviewer_form
                        }
                    }
                    tx_dataviewer_pager {
                        iconIdentifier = dataviewer-icon-dataviewer_pager
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
